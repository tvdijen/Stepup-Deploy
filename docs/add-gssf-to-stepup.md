# Adding new Gssp to Stepup
This document shows the steps to register a new generic second factor to the Stepup project. This is text was written 
during testing of the addition of the feature. The actual implementation is not described in this document.
 
Middleware
---
1. Add the new gssf to: `Stepup-Middleware/app/config/parameters.yml`. Example value for the `enabled_generic_second_factors`:
    ```yaml
    enabled_generic_second_factors:
        biometric:
            loa: 3
        tiqr:
            loa: 3
        gauth:
            loa: 2
    ```
 
Gateway
---
1.  Add the new gssf to: `Stepup-Gateway/app/config/parameters.yml`. Example value for the `enabled_generic_second_factors`:
    ```yaml
    enabled_generic_second_factors:
        biometric:
            loa: 3
        tiqr:
            loa: 3
        gauth:
            loa: 2
    ```

SelfService
---
1.  Add the new gssf to: `Stepup-SelfService/app/config/parameters.yml`. Example value for the `enabled_generic_second_factors`:
    ```yaml
    enabled_generic_second_factors:
        biometric:
            loa: 3
        tiqr:
            loa: 3
        gauth:
            loa: 2
    ```
 2. Add the new gssf to `providers` found in `Stepup-SelfService/app/config/samlstepupproviders.yml`. Example:
    ```yaml
    gauth:
        hosted:
            service_provider:
                public_key: %gssp_gauth_sp_publickey%
                private_key: %gssp_gauth_sp_privatekey%
            metadata:
                public_key: %gssp_gauth_metadata_publickey%
                private_key: %gssp_gauth_metadata_privatekey%
        remote:
            entity_id: %gssp_gauth_remote_entity_id%
            sso_url: %gssp_gauth_remote_sso_url%
            certificate: %gssp_gauth_remote_certificate%
        view_config:
            loa: %gssp_gauth_loa%
            logo: %gssp_gauth_logo%
            alt: %gssp_gauth_alt%
            title: %gssp_gauth_title%
            description: %gssp_gauth_description%
            button_use: %gssp_gauth_button_use%
            initiate_title: %gssp_gauth_initiate_title%
            initiate_button: %gssp_gauth_initiate_button%
            explanation: %gssp_gauth_initiate_title%
            authn_failed: %gssp_gauth_authn_failed%
            pop_failed: %gssp_gauth_pop_failed%
    ```  
    
3. Add the newly added parameters to `Stepup-SelfService/app/config/samlstepupproviders_parameters.yml`. Note that 
translations are specified in the parameters.
    ```yaml
    gssp_gauth_sp_publickey: '%kernel.root_dir%/../vendor/surfnet/stepup-saml-bundle/src/Resources/keys/development_publickey.cer'
    gssp_gauth_sp_privatekey: '%kernel.root_dir%/../vendor/surfnet/stepup-saml-bundle/src/Resources/keys/development_privatekey.pem'
    gssp_gauth_metadata_publickey: '%kernel.root_dir%/../vendor/surfnet/stepup-saml-bundle/src/Resources/keys/development_publickey.cer'
    gssp_gauth_metadata_privatekey: '%kernel.root_dir%/../vendor/surfnet/stepup-saml-bundle/src/Resources/keys/development_privatekey.pem'
    gssp_gauth_remote_certificate: 'The contents of the certificate published by the gssp'
    gssp_gauth_remote_entity_id: 'https://gw-dev.stepup.coin.surf.net/app_dev.php/gssp/gauth/metadata'
    gssp_gauth_remote_sso_url: 'https://gw-dev.stepup.coin.surf.net/app_dev.php/gssp/gauth/single-sign-on'
    gssp_gauth_loa: 2
    gssp_gauth_logo: /images/second-factor/gauth.png
    gssp_gauth_alt:
        en_GB: 'Gauth device'
        nl_NL: 'Gauth apparaat'
    gssp_gauth_title:
        en_GB: 'Gauth device'
        nl_NL: 'Gauth apparaat'
    gssp_gauth_description:
        en_GB: 'Log in using a Gauth device.'
        nl_NL: 'Log in met een gauth apparaat.'
    gssp_gauth_button_use:
        en_GB: Select
        nl_NL: Selecteer
    gssp_gauth_initiate_title:
        en_GB: 'Register a Gauth device'
        nl_NL: 'Registratie gauth apparaat'
    gssp_gauth_initiate_button:
        en_GB: 'Register Gauth device'
        nl_NL: 'Registreer gauth apparaat'
    gssp_gauth_explanation:
        en_GB: 'Click the button below to register a Gauth device.'
        nl_NL: 'Klik op de knop hieronder om je gauth apparaat te registreren.'
    gssp_gauth_authn_failed:
        en_GB: 'Registration of Gauth device has failed. Please try again.'
        nl_NL: 'Registratie gauth apparaat is mislukt. Probeer het nogmaals.'
    gssp_gauth_pop_failed:
        en_GB: 'Registration of your token failed. Please try again.'
        nl_NL: 'De registratie van uw token is mislukt. Probeer het nogmaals.'
    ```

## Configuring app url's for profiders
Set the Android and/or iOs URL in `samlstepupprovidres.yml` like this:

```yaml
surfnet_stepup_self_service_saml_stepup_provider:
    routes:
	...
    providers:
        tiqr:
            hosted:
                ...
            remote:
                ...
            view_config:
                ...
                description: %gssp_tiqr_description%                
                app_android_url: %gssp_tiqr_app_android_url%
                app_ios_url: %gssp_tiqr_app_ios_url%
		...
        gauth:
            hosted:
                ...
            remote:
                ...
            view_config:
                ...
                description: %gssp_gauth_description%                
                app_android_url: %gssp_gauth_app_android_url%
		...
```

Note that the app url's are optional config settings. 

When adding one of the two supported app url's the description must be set accordingly. The `samlstepupproviders_parameters.yml` file for the explanation above:

```yaml
gssp_tiqr_description:
parameters:
    gssp_tiqr_description:
        en_GB: 'Log in with a smartphone app. For all smartphones with %%ios_link_start%%Apple iOS%%ios_link_end%% or %%android_link_start%%Android%%android_link_end%%.'
        nl_NL: 'Log in met een app op je smartphone. Geschikt voor smartphones met %%ios_link_start%%Apple iOS%%ios_link_end%% of %%android_link_start%%Android%%android_link_end%%.'
gssp_gauth_description:
        en_GB: 'Log in with a Miko smartphone app. For all smartphones with %%android_link_start%%Android%%android_link_end%%.'
        nl_NL: 'Log in met een Miko app op je smartphone. Geschikt voor smartphones met %%android_link_start%%Android%%android_link_end%%.'
    
```

The application wil validate the descriptions for the correct app url tokens.

RA
---
1.  Add the new gssp to: `Stepup-RA/app/config/parameters.yml`. Example value for the `enabled_generic_second_factors`:
    ```yaml
    enabled_generic_second_factors:
        biometric:
            loa: 3
        tiqr:
            loa: 3
        gauth:
            loa: 2
    ```
2. Add the new gssp to `providers` found in `Stepup-RA/app/config/samlstepupproviders.yml`. Example:
    ```yaml
    gauth:
        hosted:
            service_provider:
                public_key: %gssp_gauth_sp_publickey%
                private_key: %gssp_gauth_sp_privatekey%
            metadata:
                public_key: %gssp_gauth_metadata_publickey%
                private_key: %gssp_gauth_metadata_privatekey%
        remote:
            entity_id: %gssp_gauth_remote_entity_id%
            sso_url: %gssp_gauth_remote_sso_url%
            certificate: %gssp_gauth_remote_certificate%
        view_config:
            page_title: %gssp_gauth_page_title%
            explanation: %gssp_gauth_explanation%
            initiate: %gssp_gauth_initiate%
            gssf_id_mismatch: %gssp_gauth_gssf_id_mismatch% 
    ```
3. Add the newly added parameters to `Stepup-RA/app/config/samlstepupproviders_parameters.yml`. Note that 
translations are specified in the parameters.
    ```yaml
     gssp_gauth_sp_publickey: /full/path/to/the/gateway-as-sp/public-key-file.cer
     gssp_gauth_sp_privatekey: /full/path/to/the/gateway-as-sp/private-key-file.pem
     gssp_gauth_metadata_publickey: /full/path/to/the/gateway-metadata/public-key-file.cer
     gssp_gauth_metadata_privatekey: /full/path/to/the/gateway-as-sp/private-key-file.pem
     gssp_gauth_remote_entity_id: 'https://actual-gssp.entity-id.tld'
     gssp_gauth_remote_sso_url: 'https://actual-gssp.entity-id.tld/single-sign-on/url'
     gssp_gauth_remote_certificate: 'The contents of the certificate published by the gssp'
     gssp_gauth_title:
          en_GB: 'EN the English name for the provider'
          nl_NL: 'NL the Dutch name for the provider'
     gssp_gauth_page_title:
         en_GB: 'EN ra.vetting.gssf.initiate.gauth.title.page'
         nl_NL: 'NL ra.vetting.gssf.initiate.gauth.title.page'
     gssp_gauth_explanation:
         en_GB: 'EN ra.vetting.gssf.initiate.gauth.text.explanation'
         nl_NL: 'NL ra.vetting.gssf.initiate.gauth.text.explanation'
     gssp_gauth_initiate:
         en_GB: 'EN ra.vetting.gssf.initiate.gauth.button.initiate'
         nl_NL: 'NL ra.vetting.gssf.initiate.gauth.button.initiate'
     gssp_gauth_gssf_id_mismatch:
         en_GB: 'EN ra.vetting.gssf.initiate.gauth.error.gssf_id_mismatch'
         nl_NL: 'NL ra.vetting.gssf.initiate.gauth.error.gssf_id_mismatch'
    ```
    
4.  The GSSP configuration in the `Stepup-Gateway/app/config/samlstepupproviders.yml` should look like:
    ```yaml
    gauth:
        enabled: %gssp_gauth_enabled%
        hosted:
            service_provider:
                public_key: %gssp_gauth_sp_publickey%
                private_key: %gssp_gauth_sp_privatekey%
            identity_provider:
                service_provider_repository: saml.entity_repository
                public_key: %gssp_gauth_idp_publickey%
                private_key: %gssp_gauth_idp_privatekey%
            metadata:
                public_key: %gssp_gauth_metadata_publickey%
                private_key: %gssp_gauth_metadata_privatekey%
        remote:
            entity_id: %gssp_gauth_remote_entity_id%
            sso_url: %gssp_gauth_remote_sso_url%
            certificate: %gssp_gauth_remote_certificate%
        view_config:
            logo: %gssp_gauth_logo%
            title: %gssp_gauth_title%
    ``` 
    
    And for the view config add for each provider, to the same parameters file:    
    ```yaml
    gssp_gauth_logo: /images/second-factor/gauth.png
    gssp_gauth_title: Gauth
    ```
    
    To be able to register and vet the token we need to instruct the gateway to proxy requests for the newly added Gssp. Enable the new GSSP by adding it to the list of enabled SP's in the Gateway configuration. In `Stepup-Gateway/app/config/samlstepupproviders_parameters.yml` add to `gssp_allowed_sps`: 
    ```yaml
    gssp_allowed_sps:
        - 'https://ra-dev.stepup.coin.surf.net/app_dev.php/vetting-procedure/gssf/gauth/metadata'
        - 'https://ss-dev.stepup.coin.surf.net/app_dev.php/registration/gssf/gauth/metadata'
    ```
5. Push a new middleware configuration, instructing the Stepup applications of the existence of the new GSSP service provider. To do so add the registration and vetting entity id's to the list of gateway serviceproviders. 

See the Postman examples provided in the Stepup-Middleware project for more details. Basically add the following configuration to the `management/configuration` JSON payload:

    ```yaml
    {
        // removed for brevity
        "gateway": {
            "service_providers": [                
                {
                    "entity_id": "https://ss-dev.stepup.coin.surf.net/app_dev.php/registration/gssf/gauth/metadata",
                    "public_key": "MIIEJTCCAw2gAwIBAgIJANug+o++1X5IMA0GCSqGSIb3DQEBCwUAMIGoMQswCQYDVQQGEwJOTDEQMA4GA1UECAwHVXRyZWNodDEQMA4GA1UEBwwHVXRyZWNodDEVMBMGA1UECgwMU1VSRm5ldCBCLlYuMRMwEQYDVQQLDApTVVJGY29uZXh0MRwwGgYDVQQDDBNTVVJGbmV0IERldmVsb3BtZW50MSswKQYJKoZIhvcNAQkBFhxzdXJmY29uZXh0LWJlaGVlckBzdXJmbmV0Lm5sMB4XDTE0MTAyMDEyMzkxMVoXDTE0MTExOTEyMzkxMVowgagxCzAJBgNVBAYTAk5MMRAwDgYDVQQIDAdVdHJlY2h0MRAwDgYDVQQHDAdVdHJlY2h0MRUwEwYDVQQKDAxTVVJGbmV0IEIuVi4xEzARBgNVBAsMClNVUkZjb25leHQxHDAaBgNVBAMME1NVUkZuZXQgRGV2ZWxvcG1lbnQxKzApBgkqhkiG9w0BCQEWHHN1cmZjb25leHQtYmVoZWVyQHN1cmZuZXQubmwwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDXuSSBeNJY3d4p060oNRSuAER5nLWT6AIVbv3XrXhcgSwc9m2b8u3ksp14pi8FbaNHAYW3MjlKgnLlopYIylzKD/6Ut/clEx67aO9Hpqsc0HmIP0It6q2bf5yUZ71E4CN2HtQceO5DsEYpe5M7D5i64kS2A7e2NYWVdA5Z01DqUpQGRBc+uMzOwyif6StBiMiLrZH3n2r5q5aVaXU4Vy5EE4VShv3Mp91sgXJj/v155fv0wShgl681v8yf2u2ZMb7NKnQRA4zM2Ng2EUAyy6PQ+Jbn+rALSm1YgiJdVuSlTLhvgwbiHGO2XgBi7bTHhlqSrJFK3Gs4zwIsop/XqQRBAgMBAAGjUDBOMB0GA1UdDgQWBBQCJmcoa/F7aM3jIFN7Bd4uzWRgzjAfBgNVHSMEGDAWgBQCJmcoa/F7aM3jIFN7Bd4uzWRgzjAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQBd80GpWKjp1J+Dgp0blVAox1s/WPWQlex9xrx1GEYbc5elp3svS+S82s7dFm2llHrrNOBt1HZVC+TdW4f+MR1xq8O5lOYjDRsosxZc/u9jVsYWYc3M9bQAx8VyJ8VGpcAK+fLqRNabYlqTnj/t9bzX8fS90sp8JsALV4g84Aj0G8RpYJokw+pJUmOpuxsZN5U84MmLPnVfmrnuCVh/HkiLNV2c8Pk8LSomg6q1M1dQUTsz/HVxcOhHLj/owwh3IzXf/KXV/E8vSYW8o4WWCAnruYOWdJMI4Z8NG1Mfv7zvb7U3FL1C/KLV04DqzALXGj+LVmxtDvuxqC042apoIDQV",
                    "acs": [
                        "https://ss-dev.stepup.coin.surf.net/app_dev.php/registration/gssf/gauth/consume-assertion"
                    ], 
                    "loa": {
                        "__default__": "https://gw-dev.stepup.coin.surf.net/authentication/loa1"
                    },
                    "assertion_encryption_enabled": false,
                    "blacklisted_encryption_algorithms": [],
                    "second_factor_only": false,
                    "second_factor_only_nameid_patterns": []
                },
                {
                    "entity_id": "https://ra-dev.stepup.coin.surf.net/app_dev.php/vetting-procedure/gssf/gauth/metadata",
                    "public_key": "MIIEJTCCAw2gAwIBAgIJANug+o++1X5IMA0GCSqGSIb3DQEBCwUAMIGoMQswCQYDVQQGEwJOTDEQMA4GA1UECAwHVXRyZWNodDEQMA4GA1UEBwwHVXRyZWNodDEVMBMGA1UECgwMU1VSRm5ldCBCLlYuMRMwEQYDVQQLDApTVVJGY29uZXh0MRwwGgYDVQQDDBNTVVJGbmV0IERldmVsb3BtZW50MSswKQYJKoZIhvcNAQkBFhxzdXJmY29uZXh0LWJlaGVlckBzdXJmbmV0Lm5sMB4XDTE0MTAyMDEyMzkxMVoXDTE0MTExOTEyMzkxMVowgagxCzAJBgNVBAYTAk5MMRAwDgYDVQQIDAdVdHJlY2h0MRAwDgYDVQQHDAdVdHJlY2h0MRUwEwYDVQQKDAxTVVJGbmV0IEIuVi4xEzARBgNVBAsMClNVUkZjb25leHQxHDAaBgNVBAMME1NVUkZuZXQgRGV2ZWxvcG1lbnQxKzApBgkqhkiG9w0BCQEWHHN1cmZjb25leHQtYmVoZWVyQHN1cmZuZXQubmwwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDXuSSBeNJY3d4p060oNRSuAER5nLWT6AIVbv3XrXhcgSwc9m2b8u3ksp14pi8FbaNHAYW3MjlKgnLlopYIylzKD/6Ut/clEx67aO9Hpqsc0HmIP0It6q2bf5yUZ71E4CN2HtQceO5DsEYpe5M7D5i64kS2A7e2NYWVdA5Z01DqUpQGRBc+uMzOwyif6StBiMiLrZH3n2r5q5aVaXU4Vy5EE4VShv3Mp91sgXJj/v155fv0wShgl681v8yf2u2ZMb7NKnQRA4zM2Ng2EUAyy6PQ+Jbn+rALSm1YgiJdVuSlTLhvgwbiHGO2XgBi7bTHhlqSrJFK3Gs4zwIsop/XqQRBAgMBAAGjUDBOMB0GA1UdDgQWBBQCJmcoa/F7aM3jIFN7Bd4uzWRgzjAfBgNVHSMEGDAWgBQCJmcoa/F7aM3jIFN7Bd4uzWRgzjAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQBd80GpWKjp1J+Dgp0blVAox1s/WPWQlex9xrx1GEYbc5elp3svS+S82s7dFm2llHrrNOBt1HZVC+TdW4f+MR1xq8O5lOYjDRsosxZc/u9jVsYWYc3M9bQAx8VyJ8VGpcAK+fLqRNabYlqTnj/t9bzX8fS90sp8JsALV4g84Aj0G8RpYJokw+pJUmOpuxsZN5U84MmLPnVfmrnuCVh/HkiLNV2c8Pk8LSomg6q1M1dQUTsz/HVxcOhHLj/owwh3IzXf/KXV/E8vSYW8o4WWCAnruYOWdJMI4Z8NG1Mfv7zvb7U3FL1C/KLV04DqzALXGj+LVmxtDvuxqC042apoIDQV",
                    "acs": [
                        "https://ra-dev.stepup.coin.surf.net/app_dev.php/vetting-procedure/gssf/gauth/verify"
                    ], 
                    "loa": {
                        "__default__": "https://gw-dev.stepup.coin.surf.net/authentication/loa1"
                    },
                    "assertion_encryption_enabled": false,
                    "blacklisted_encryption_algorithms": [],
                    "second_factor_only": false,
                    "second_factor_only_nameid_patterns": []
                },
            ]
    ```