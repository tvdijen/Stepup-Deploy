<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\MinkExtension\Context\MinkContext;

class SecondFactorAuthContext implements Context
{
    const SSO_IDP = 'https://gateway.stepup.example.com/authentication/metadata';
    const SFO_IDP = 'https://gateway.stepup.example.com/second-factor-only/metadata';
    const SSO_SP = 'default-sp';
    const SFO_SP = 'second-sp';
    const TEST_NAMEID = 'urn:collab:person:institution-a.example.com:joe-a1';

    /**
     * @var \Behat\MinkExtension\Context\MinkContext
     */
    private $minkContext;

    /**
     * @var string
     */
    private $spTestUrl;

    /**
     * @var string
     */
    private $activeIdp;

    /**
     * @var string
     */
    private $activeSp;

    /**
     * @var int
     */
    private $requiredLoa;

    /**
     * Initializes context.
     */
    public function __construct($spTestUrl)
    {
        $this->spTestUrl = $spTestUrl;
    }

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->minkContext = $environment->getContext(MinkContext::class);
    }

    /**
     * @Given a service provider configured for second-factor-only
     */
    public function configureServiceProviderForSecondFactorOnly()
    {
        $this->activeIdp = self::SFO_IDP;
        $this->activeSp = self::SFO_SP;
        $this->requiredLoa = 2;
    }

    /**
     * @Given a service provider configured for single-signon
     */
    public function configureServiceProviderForSingleSignOn()
    {
        $this->activeIdp = self::SSO_IDP;
        $this->activeSp = self::SSO_SP;
        $this->requiredLoa = 2;
    }

    /**
     * @When I visit the service provider
     */
    public function visitServiceProvider()
    {
        $this->minkContext->visit($this->spTestUrl);

        $this->minkContext->fillField('idp', $this->activeIdp);
        $this->minkContext->fillField('sp', $this->activeSp);
        $this->minkContext->fillField('loa', $this->requiredLoa);

        if ($this->activeIdp === self::SFO_IDP) {
            $this->minkContext->fillField('subject', self::TEST_NAMEID);
        }

        $this->minkContext->pressButton('Login');
    }

    /**
     * @Given the service provider requires no second factor
     */
    public function setImplicitLoaOnServiceProvider()
    {
        $this->requiredLoa = 1;
    }

    /**
     * @When I verify the second factor
     */
    public function verifySecondFactor()
    {
        $this->selectDummySecondFactorOnTokenSelectionScreen();
        $this->authenticateUserInDummyGsspApplication();
        $this->passTroughGatewayProxyAssertionConsumerService();
    }
    /**
     * @When I verify the Yubikey second factor
     */
    public function verifyYuikeySecondFactor()
    {
        $this->authenticateUserYubikeyInGateway();
        // Pass through the 'return to sp' redirection page.
        $this->minkContext->pressButton('Submit');
    }

    /**
     * @When I cancel the second factor authentication
     */
    public function cancelSecondFactorAuthentication()
    {
        $this->selectDummySecondFactorOnTokenSelectionScreen();
        $this->cancelAuthenticationInDummyGsspApplication();
        $this->passTroughGatewayProxyAssertionConsumerService();
    }

    /**
     * @Then second factor authentication is not initiated
     */
    public function secondFactorAuthenticationIsNotInitiated()
    {
        $this->passTroughGatewaySsoAssertionConsumerService();
    }

    public function selectDummySecondFactorOnTokenSelectionScreen()
    {
        $this->minkContext->pressButton('gateway_choose_second_factor_choose_dummy');
    }

    public function selectYubikeySecondFactorOnTokenSelectionScreen()
    {
        $this->minkContext->pressButton('gateway_choose_second_factor_choose_yubikey');
    }

    public function authenticateUserInDummyGsspApplication()
    {
        $this->minkContext->assertPageAddress('http://localhost:1234/authentication');

        // Trigger the dummy authentication action.
        $this->minkContext->pressButton('Authenticate user');

        // Pass through the 'return to sp' redirection page.
        $this->minkContext->pressButton('Submit');
    }

    public function authenticateUserYubikeyInGateway()
    {
        $this->minkContext->assertPageAddress('https://gateway.stepup.example.com/verify-second-factor/yubikey');

        // Give an OTP
        $this->minkContext->fillField('gateway_verify_yubikey_otp_otp', 'ccccccdhgrbtucnfhrhltvfkchlnnrndcbnfnnljjdgf');
        // Simulate the enter press the yubikey otp generator
        $form = $this->minkContext->getSession()->getPage()->find('css', '[name="gateway_verify_yubikey_otp"]');
        if (!$form) {
            throw new ElementNotFoundException('Yubikey OTP Submit form could not be found on the page');
        }
        $form->submit();
    }

    public function cancelAuthenticationInDummyGsspApplication()
    {
        $this->minkContext->assertPageAddress('http://localhost:1234/authentication');

        // Cancel the dummy authentication action.
        $this->minkContext->pressButton('Return authentication failed');

        // Pass through the 'return to sp' redirection page.
        $this->minkContext->pressButton('Submit');
    }

    public function passTroughGatewaySsoAssertionConsumerService()
    {
        $this->minkContext->assertPageAddress('https://gateway.stepup.example.com/authentication/consume-assertion');

        $this->minkContext->pressButton('Submit');
    }

    public function passTroughGatewayProxyAssertionConsumerService()
    {
        $this->minkContext->assertPageAddress('https://gateway.stepup.example.com/gssp/dummy/consume-assertion');

        $this->minkContext->pressButton('Submit');
    }

    /**
     * @When I authenticate with the identity provider
     */
    public function authenticateWithIdentityProvider()
    {
        $this->minkContext->assertPageAddress('https://ssp.stepup.example.com/module.php/core/loginuserpass.php');

        $this->minkContext->fillField('username', 'joe-a1');
        $this->minkContext->fillField('password', 'joe-a1');

        $this->minkContext->pressButton('Login');

        $this->passTroughIdentityProviderAssertionConsumerService();
    }
    /**
     * @When Admin authenticates with the identity provider
     */
    public function authenticateWithIdentityProviderAsAdmin()
    {
        $this->minkContext->assertPageAddress('https://ssp.stepup.example.com/module.php/core/loginuserpass.php');

        $this->minkContext->fillField('username', 'admin');
        $this->minkContext->fillField('password', 'admin');

        $this->minkContext->pressButton('Login');

        $this->passTroughIdentityProviderAssertionConsumerService();
    }

    private function passTroughIdentityProviderAssertionConsumerService()
    {
        $this->minkContext->assertPageAddress('https://ssp.stepup.example.com/module.php/core/loginuserpass.php');

        $this->minkContext->pressButton('Submit');
    }

    /**
     * @Then I am logged on the service provider
     */
    public function assertLoggedInOnServiceProvider()
    {
        $this->minkContext->assertPageAddress('https://ssp.stepup.example.com/sp.php');

        $this->minkContext->assertPageContainsText(
            sprintf('You are logged in to SP')
        );
    }

    /**
     * @Then I see an error at the service provider
     */
    public function assertErrorAtServiceProvider()
    {
        $this->minkContext->assertPageAddress('https://ssp.stepup.example.com/module.php/saml/sp/saml2-acs.php/default-sp');

        $this->minkContext->assertPageContainsText(
            sprintf('Unhandled exception')
        );

        $this->minkContext->assertPageNotContainsText(
            sprintf('You are logged in to SP')
        );
    }
}
