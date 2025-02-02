# Symfony OAuth2 Bundle

[🇹🇷 Türkçe](#tr) | [🇬🇧 English](#en)

## Navigation
- [Türkçe](#tr)
  - [Kurulum](#tr-kurulum)
  - [Konfigürasyon](#tr-konfigurasyon)
  - [Docker](#tr-docker)
  - [Kullanım](#tr-kullanim)
  - [Rotalar](#tr-rotalar)
  - [Özelleştirme](#tr-ozellestirme)
  - [Kaldırma](#tr-kaldirma)
  - [Servisler](#tr-servisler)
- [English](#en)
  - [Installation](#en-installation)
  - [Configuration](#en-configuration)
  - [Docker](#en-docker)
  - [Usage](#en-usage)
  - [Routes](#en-routes)
  - [Customization](#en-customization)
  - [Uninstall](#en-uninstall)
  - [Services](#en-services)

# TR

Bu bundle, OAuth2 kimlik doğrulama işlemlerini Symfony uygulamanıza entegre etmenizi sağlar.

[⬆️ Başa Dön](#navigation)

## TR Kurulum

Composer ile yükleyin:

```bash
composer require bekirozturk/symfony-oauth2-bundle
```

[⬆️ Başa Dön](#navigation)

## TR Konfigürasyon

Bundle, Symfony Flex tarafından otomatik olarak `config/bundles.php` dosyasına eklenecektir. Eğer otomatik olarak eklenmemişse, manuel olarak aşağıdaki satırı ekleyebilirsiniz:

```php
return [
    // ...
    Symfony\Bundle\OAuth2Bundle\SymfonyOAuth2Bundle::class => ['all' => true],
];
```

2. `.env` dosyanızda aşağıdaki değişkenleri tanımlayın:

> **Önemli Not**: Aşağıdaki değişkenlerin değerlerini SSO sağlayıcınızdan (Identity Provider) almanız gerekmektedir. Bu bilgiler SSO entegrasyonu için gerekli olan endpoint URL'leri ve kimlik bilgileridir.

```env
# SSO sağlayıcısından almanız gereken kimlik bilgileri
OAUTH_CLIENT_ID=your_client_id          # SSO uygulamanızın client ID'si
OAUTH_CLIENT_SECRET=your_client_secret   # SSO uygulamanızın client secret'ı

# SSO sağlayıcısının endpoint URL'leri (SSO sağlayıcısından alınmalıdır)
OAUTH_AUTHORIZE_URL=http://sso-provider/oauth2/authorize   # Yetkilendirme endpoint'i
OAUTH_TOKEN_URL=http://sso-provider/oauth2/token          # Token endpoint'i
OAUTH_USERINFO_URL=http://sso-provider/api/userinfo       # Kullanıcı bilgileri endpoint'i

# SSO sağlayıcısı tarafından desteklenen ve size verilen değerler
OAUTH_SCOPE=your_scope                   # İzin istenen kapsamlar (örn: "openid profile email")
OAUTH_CODE_CHALLENGE_METHOD=S256         # PKCE metodu (genellikle S256)
OAUTH_RESPONSE_TYPE=code                 # Response type (genellikle "code")
OAUTH_GRANT_TYPE=authorization_code      # Grant type (genellikle "authorization_code")

# Sizin uygulamanızın callback URL'i (SSO sağlayıcısına bildirmeniz gerekir)
OAUTH_REDIRECT_URI=http://localhost/oauth2/check
```

> **Önemli Not**: 
> 1. `OAUTH_REDIRECT_URI` değeri, SSO entegrasyonu sırasında SSO sağlayıcısına bildirmeniz gereken callback URL'idir. Bu URL, kullanıcı başarılı bir şekilde kimlik doğrulaması yaptıktan sonra SSO sağlayıcısının kullanıcıyı yönlendireceği adrestir. SSO sağlayıcısının güvenlik ayarlarında bu URL'i izin verilen redirect URI'lar listesine eklemeniz gerekmektedir.
> 2. Diğer tüm endpoint URL'leri ve konfigürasyon değerleri SSO sağlayıcınız tarafından size verilmelidir. Bu değerler SSO sağlayıcısının OAuth2 sistemine göre değişiklik gösterebilir.

[⬆️ Başa Dön](#navigation)

## TR Docker

Eğer uygulamanızı Docker ortamında çalıştırıyorsanız ve SSO sunucunuz da yerel makinenizde (localhost) çalışıyorsa, aşağıdaki adımları takip edin:

1. Docker container'ınızın host makineye erişebilmesi için `docker-compose.yml` dosyanızda PHP servisine aşağıdaki ayarı ekleyin:

```yaml
services:
    php:
        # ... diğer ayarlar ...
        extra_hosts:
            - "host.docker.internal:host-gateway"  # Host makineye erişim için gerekli
```

2. `.env` dosyanızda aşağıdaki konfigürasyonu kullanın:

```env
# SSO sağlayıcısından almanız gereken kimlik bilgileri
OAUTH_CLIENT_ID=your_client_id
OAUTH_CLIENT_SECRET=your_client_secret

# Docker içinden host makineye erişim için host.docker.internal kullanın
OAUTH_AUTHORIZE_URL=http://host.docker.internal:9002/oauth2/authorize
OAUTH_TOKEN_URL=http://host.docker.internal:9002/oauth2/token
OAUTH_USERINFO_URL=http://host.docker.internal:9002/api/userinfo

# Callback URL'i host makineden erişileceği şekilde ayarlayın
OAUTH_REDIRECT_URI=http://localhost/oauth2/check

# Diğer ayarlar
OAUTH_SCOPE=your_scope
OAUTH_CODE_CHALLENGE_METHOD=S256
OAUTH_RESPONSE_TYPE=code
OAUTH_GRANT_TYPE=authorization_code
```

> **Önemli Not**: 
> 1. Docker container'ından host makineye erişmek için `localhost` yerine `host.docker.internal` kullanılmalıdır.
> 2. `OAUTH_REDIRECT_URI` değeri, tarayıcıdan erişileceği için `localhost` olarak kalmalıdır.
> 3. Port numaralarını (örnekte 9002) kendi SSO sunucunuzun çalıştığı porta göre ayarlayın.

[⬆️ Başa Dön](#navigation)

## TR Kullanım

OAuth2 login sayfasına yönlendirmek için:

```php
// Controller'ınızda
use Symfony\Bundle\OAuth2Bundle\Controller\AuthController;

return $this->redirectToRoute('oauth2_authorize');
```

veya Twig template'inizde:

```twig
<a href="{{ path('oauth2_authorize') }}">OAuth2 ile Giriş Yap</a>
```

[⬆️ Başa Dön](#navigation)

## TR Rotalar

Bundle aşağıdaki rotaları tanımlar:

- `/oauth2/authorize`: OAuth2 sunucusuna yönlendirme (oauth2_authorize)
- `/oauth2/check`: OAuth2 sunucusundan dönüş noktası (oauth2_check)
- `/oauth2/success`: Başarılı kimlik doğrulama sonrası yönlendirme (app_oauth2_success)

[⬆️ Başa Dön](#navigation)

## TR Özelleştirme

Bundle, başarılı kimlik doğrulama sonrası `/oauth2/success` endpoint'ine yönlendirir. Bu endpoint'i kendi ihtiyaçlarınıza göre özelleştirebilirsiniz. Örneğin:

```php
// src/Controller/SymfonyOauth2Controller.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SymfonyOauth2Controller extends AbstractController
{
    #[Route('/oauth2/success', name: 'app_oauth2_success')]
    public function success(): Response
    {
        // Session'dan kullanıcı bilgilerini al
        $userInfo = $this->get('session')->get('oauth2_user_info');
        
        // Kullanıcı bilgilerini kullanarak kendi login mantığınızı uygulayın
        // Örneğin:
        // - Kullanıcıyı veritabanında kontrol edin
        // - Yeni kullanıcı oluşturun
        // - Oturum açın
        // - Başka bir sayfaya yönlendirin
        
        return $this->render('oauth2/success.html.twig', [
            'user_info' => $userInfo
        ]);
    }
}
```

[⬆️ Başa Dön](#navigation)

## TR Kaldırma

Bundle'ı kaldırdığınızda, aşağıdaki dosyaları manuel olarak silmeniz gerekir:

```bash
# Konfigürasyon dosyalarını sil
rm config/packages/symfony_oauth2.yaml
rm config/routes/symfony_oauth2.yaml

# Controller dosyasını sil
rm src/Controller/SymfonyOauth2Controller.php
```

Ardından bundle'ı composer.json'dan kaldırın:

```bash
composer remove bekirozturk/symfony-oauth2-bundle
```

[⬆️ Başa Dön](#navigation)

## TR Servisler

Bundle aşağıdaki servisleri sağlar:

### OAuth2Service

OAuth2Service, SSO entegrasyonu için gerekli temel metodları sağlar:

```php
use Symfony\Bundle\OAuth2Bundle\Service\OAuth2Service;

class YourController
{
    private $oauthService;

    public function __construct(OAuth2Service $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    public function someAction()
    {
        // SSO yetkilendirme URL'ini oluşturma
        $state = bin2hex(random_bytes(16));
        $codeVerifier = bin2hex(random_bytes(32));
        $authUrl = $this->oauthService->getAuthorizationUrl($state, $codeVerifier);

        // Access token alma
        $tokenData = $this->oauthService->requestAccessToken($code, $codeVerifier);
        
        // Kullanıcı bilgilerini alma
        $userInfo = $this->oauthService->getUserInfo($tokenData['access_token']);
    }
}
```

OAuth2Service şu metodları sağlar:

- `getAuthorizationUrl(string $state, string $codeVerifier)`: PKCE destekli OAuth2 yetkilendirme URL'ini oluşturur
- `requestAccessToken(string $code, string $codeVerifier)`: Authorization code ile access token alır
- `getUserInfo(string $accessToken)`: Access token kullanarak kullanıcı bilgilerini getirir

[⬆️ Başa Dön](#navigation)

## License / Lisans

MIT 

# EN

This bundle allows you to integrate OAuth2 authentication into your Symfony application.

[⬆️ Back to Top](#navigation)

## EN Installation

Install with Composer:

```bash
composer require bekirozturk/symfony-oauth2-bundle
```

[⬆️ Back to Top](#navigation)

## EN Configuration

The bundle will be automatically added to your `config/bundles.php` by Symfony Flex. If not, you can add it manually:

```php
return [
    // ...
    Symfony\Bundle\OAuth2Bundle\SymfonyOAuth2Bundle::class => ['all' => true],
];
```

2. Define the following variables in your `.env` file:

> **Important Note**: You need to obtain the following values from your SSO provider (Identity Provider). These are the endpoint URLs and credentials required for SSO integration.

```env
# Credentials from your SSO provider
OAUTH_CLIENT_ID=your_client_id          # Your SSO application's client ID
OAUTH_CLIENT_SECRET=your_client_secret   # Your SSO application's client secret

# SSO provider endpoint URLs (must be obtained from SSO provider)
OAUTH_AUTHORIZE_URL=http://sso-provider/oauth2/authorize   # Authorization endpoint
OAUTH_TOKEN_URL=http://sso-provider/oauth2/token          # Token endpoint
OAUTH_USERINFO_URL=http://sso-provider/api/userinfo       # User info endpoint

# Values supported and provided by your SSO provider
OAUTH_SCOPE=your_scope                   # Requested scopes (e.g., "openid profile email")
OAUTH_CODE_CHALLENGE_METHOD=S256         # PKCE method (usually S256)
OAUTH_RESPONSE_TYPE=code                 # Response type (usually "code")
OAUTH_GRANT_TYPE=authorization_code      # Grant type (usually "authorization_code")

# Your application's callback URL (must be registered with SSO provider)
OAUTH_REDIRECT_URI=http://localhost/oauth2/check
```

> **Important Note**: 
> 1. The `OAUTH_REDIRECT_URI` value must be registered with your SSO provider. This URL is where the SSO provider will redirect users after successful authentication. You need to add this URL to the list of allowed redirect URIs in your SSO provider's security settings.
> 2. All other endpoint URLs and configuration values must be provided by your SSO provider. These values may vary depending on the OAuth2 system of your SSO provider.

[⬆️ Back to Top](#navigation)

## EN Docker

If you're running your application in Docker and your SSO server is running on your local machine (localhost), follow these steps:

1. Add the following configuration to your PHP service in `docker-compose.yml`:

```yaml
services:
    php:
        # ... other settings ...
        extra_hosts:
            - "host.docker.internal:host-gateway"  # Required for host machine access
```

2. Use the following configuration in your `.env` file:

```env
# Credentials from your SSO provider
OAUTH_CLIENT_ID=your_client_id
OAUTH_CLIENT_SECRET=your_client_secret

# Use host.docker.internal for accessing host machine from Docker
OAUTH_AUTHORIZE_URL=http://host.docker.internal:9002/oauth2/authorize
OAUTH_TOKEN_URL=http://host.docker.internal:9002/oauth2/token
OAUTH_USERINFO_URL=http://host.docker.internal:9002/api/userinfo

# Set callback URL as it will be accessed from browser
OAUTH_REDIRECT_URI=http://localhost/oauth2/check

# Other settings
OAUTH_SCOPE=your_scope
OAUTH_CODE_CHALLENGE_METHOD=S256
OAUTH_RESPONSE_TYPE=code
OAUTH_GRANT_TYPE=authorization_code
```

> **Important Note**: 
> 1. Use `host.docker.internal` instead of `localhost` to access the host machine from Docker container.
> 2. Keep `OAUTH_REDIRECT_URI` as `localhost` since it will be accessed from the browser.
> 3. Adjust port numbers (9002 in the example) according to your SSO server's port.

[⬆️ Back to Top](#navigation)

## EN Usage

To redirect to the OAuth2 login page:

```php
// In your controller
use Symfony\Bundle\OAuth2Bundle\Controller\AuthController;

return $this->redirectToRoute('oauth2_authorize');
```

or in your Twig template:

```twig
<a href="{{ path('oauth2_authorize') }}">Login with OAuth2</a>
```

[⬆️ Back to Top](#navigation)

## EN Routes

The bundle defines the following routes:

- `/oauth2/authorize`: Redirect to OAuth2 server (oauth2_authorize)
- `/oauth2/check`: OAuth2 server callback endpoint (oauth2_check)
- `/oauth2/success`: Successful authentication redirect endpoint (app_oauth2_success)

[⬆️ Back to Top](#navigation)

## EN Customization

After successful authentication, the bundle redirects to the `/oauth2/success` endpoint. You can customize this endpoint according to your needs. For example:

```php
// src/Controller/SymfonyOauth2Controller.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SymfonyOauth2Controller extends AbstractController
{
    #[Route('/oauth2/success', name: 'app_oauth2_success')]
    public function success(): Response
    {
        // Get user info from session
        $userInfo = $this->get('session')->get('oauth2_user_info');
        
        // Implement your own login logic using user info
        // For example:
        // - Check user in database
        // - Create new user
        // - Log in
        // - Redirect to another page
        
        return $this->render('oauth2/success.html.twig', [
            'user_info' => $userInfo
        ]);
    }
}
```

[⬆️ Back to Top](#navigation)

## EN Uninstall

When uninstalling the bundle, you need to manually remove the following files:

```bash
# Remove configuration files
rm config/packages/symfony_oauth2.yaml
rm config/routes/symfony_oauth2.yaml

# Remove controller file
rm src/Controller/SymfonyOauth2Controller.php
```

Then remove the bundle from composer.json:

```bash
composer remove bekirozturk/symfony-oauth2-bundle
```

[⬆️ Back to Top](#navigation)

## EN Services

The bundle provides the following services:

### OAuth2Service

OAuth2Service provides the core methods needed for SSO integration:

```php
use Symfony\Bundle\OAuth2Bundle\Service\OAuth2Service;

class YourController
{
    private $oauthService;

    public function __construct(OAuth2Service $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    public function someAction()
    {
        // Generate SSO authorization URL
        $state = bin2hex(random_bytes(16));
        $codeVerifier = bin2hex(random_bytes(32));
        $authUrl = $this->oauthService->getAuthorizationUrl($state, $codeVerifier);

        // Get access token
        $tokenData = $this->oauthService->requestAccessToken($code, $codeVerifier);
        
        // Get user information
        $userInfo = $this->oauthService->getUserInfo($tokenData['access_token']);
    }
}
```

OAuth2Service provides the following methods:

- `getAuthorizationUrl(string $state, string $codeVerifier)`: Generates PKCE-enabled OAuth2 authorization URL
- `requestAccessToken(string $code, string $codeVerifier)`: Gets access token using authorization code
- `getUserInfo(string $accessToken)`: Gets user information using access token

[⬆️ Back to Top](#navigation)

## License / Lisans

MIT 