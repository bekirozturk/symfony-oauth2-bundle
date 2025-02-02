<?php

/**
 * This file is part of the Symfony OAuth2 Bundle.
 * 
 * Controller for handling OAuth2 authentication flow.
 *
 * @package     Symfony\Bundle\OAuth2Bundle\Controller
 * @author      Bekir ÖZTÜRK <bekirozturk@live.com>
 * @website     https://bekirozturk.com
 * @linkedin    https://www.linkedin.com/in/ozturkbekir/
 * @github      https://github.com/bekirozturk
 * @copyright   2025 Bekir ÖZTÜRK
 * @license     MIT License
 * @version     1.0.0
 * @since       2025-02-02
 */

namespace Symfony\Bundle\OAuth2Bundle\Controller;

use Symfony\Bundle\OAuth2Bundle\Service\OAuth2Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    private $oauthService;
    private $session;

    public function __construct(OAuth2Service $oauthService, SessionInterface $session)
    {
        $this->oauthService = $oauthService;
        $this->session = $session;
    }

    /**
     * @Route("/oauth2/authorize", name="oauth2_authorize")
     */
    public function redirectToAuthorization(): Response
    {
        // PKCE için Code Verifier oluştur
        $codeVerifier = bin2hex(random_bytes(32));
        $state = bin2hex(random_bytes(16));

        // Session'a kaydet
        $this->session->set('oauth2_code_verifier', $codeVerifier);
        $this->session->set('oauth2_state', $state);

        // Yetkilendirme URL'ine yönlendir
        $authUrl = $this->oauthService->getAuthorizationUrl($state, $codeVerifier);

        return $this->redirect($authUrl);
    }

    /**
     * @Route("/oauth2/check", name="oauth2_check", methods={"GET"})
     */
    public function handleRedirect(Request $request): Response
    {
        $code = $request->query->get('code');
        $state = $request->query->get('state');
        $savedState = $this->session->get('oauth2_state');
        $codeVerifier = $this->session->get('oauth2_code_verifier');

        if (!$code || !$state || !$savedState || $state !== $savedState) {
            throw new \RuntimeException('Invalid OAuth state');
        }

        // Access Token al
        $tokenData = $this->oauthService->requestAccessToken($code, $codeVerifier);
        
        // Access Token'ı session'a kaydet
        $this->session->set('oauth2_access_token', $tokenData['access_token']);

        // Kullanıcı bilgilerini al
        $userInfo = $this->oauthService->getUserInfo($tokenData['access_token']);

        // UserInfo'yu session'a kaydet
        $this->session->set('oauth2_user_info', $userInfo);

        // /oauth2/success endpoint'ine yönlendir
        return $this->redirectToRoute('app_oauth2_success');
    }
} 