BecklynFacebookBundle
=====================

This bundle provides basic components for the usage with facebook.

**This bundle is deprecated and unmaintained.**


## Configuration overview

Including their default values.

```yaml
becklyn_facebook:
    add_p3p_headers: false      # automatically add P3P Headers to every response (details further down)
```


## Facebook App Model
A model which handles a lot of app ("real" app or page tab app) related logic.

#### Definition
```yaml
my_facebook_service:
    class: \Facebook
    arguments: [{ appId: "123456", secret: "thisisyoursecret" }]

facebook_app_model:
    class: Becklyn\FacebookBundle\Model\FacebookAppModel
    arguments:
        - @my_facebook_service
        - @session
        - @router
        - "https://www.facebook.com/Symfony2Framework"          # the fan page url
        - ["email", "user_birthday", "publish_stream"]          # your required permissions
        - "session_identifier"                                  # if you need to use multiple services, you need to define unique session identifiers
```

This class bundles a lot of the functionality connected to app development with Facebook.
It automatically loads the user api data (from a `/me` request), user request data (from the signed request) and the page data (also from the signed request).
Also these values are persistent in the session, so you can safely navigate inside your app iframe without losing all the data.

This data is capsuled in three value objects:

* `Page`
    * Bundles the page like, page admin and page id
* `RequestUser`
    * Age, country  & locale
* `ApiUser`
    * Everything from the `/me` request. Includes direct getters for the most frequent used data (from base permissions + `email` + `user_birthday`)

All value objects always contain the complete data as provided by Facebook. You can either use the direct getters or use the `getByKey` to get data for a value without a direct getter (like if you use more permissions).


Furthermore the App Model provides the following:

* Direct access to flags, whether the user has liked the page and provided the permissions
* Getter whether the user is looking at the app in facebook, but not inside your page (like if the user looks at the page as real app and not as page tab, is not persisted between page changes)
* Page Tab URL generator (including embedding data in `app_data`)
* `app_data` access
* Wrapper for wall posts


## Twig Extensions

### Facebook Utilities Twig Extension

#### Definition
This extension is automatically registered.

#### Usage
Provides the following Twig functions:

* `{{ fb_likeButton(url, dataAttributes = {}) }}`
  Generates the HTML of a facebook button. You can set all data-attributes in the second argument (omit the `data`, so provide `{href: "..."}` for `data-href="..."`)
* `{{ fb_profileImage(facebookId) }}`
  Generates the URL to the profile image for the given facebook id.
* `{{ fb_profileUrl(facebookId) }}`
  Generates the URL to the profile page for the given facebook id.
* `{{ fb_truncateLikeDescriptionText(text, length = 80) }}`
  Truncates the given text and strips all HTML tags from it. The second parameter defines, where the text is truncated ("..." is appended if the text is truncated).
  The truncation will break words.


### Facebook App Twig Extension
Provides twig functions which are related to FacebookAppModel.

#### Definition
```yaml
twig.extension.facebook_app:
    class: Becklyn\FacebookBundle\Service\FacebookAppTwigExtension
    tags:
        - { name: twig.extension }
    arguments: [@facebook_app_model]    # definition from above
```

#### Usage
Provides the following Twig functions:

* `{{ fb_permissionsData(redirectRoute, redirectRouteParameters = {}) }}`
  Returns an array with two keys: `{"hasPermissions": false, "permissionsUrl": "..."}`. Can be used json encoded to implement a permissions switch in your app.
  Expects the redirect route (with parameters) which are needed for the redirect uri used in the generation of the facebook login url.
* `{{ fb_appId() }}`
  Returns the app id as defined in the model.

#### Additional information
If you use multiple facebook app models, you can automatically prefix these twig functions. The session identifier is used for the prefixing

```yaml
facebook_app_model_shop:
    class: Becklyn\FacebookBundle\Model\FacebookAppModel
    arguments:
        # ...
        - "shop"        # the session identifier

twig.extension.facebook_app_shop:
    class: Becklyn\FacebookBundle\Service\FacebookAppTwigExtension
    tags:
        - { name: twig.extension }
    arguments: [@facebook_app_model_shop, true]    # The true marks that the functions should be prefixed
```

The twig functions will now be called:
```twig
{{ fb_shop_permissionsData(redirectRoute, redirectRouteParameters = {}) }}
{{ fb_shop_appId() }}
```


## CSS & SCSS
Only one scss file is included, which sets the width and the overflow for an element with the id `#fb-wrap`, to automatically fit in the app iframe.
You can `@import` it into your own SCSS file.



## P3P Header Listener (Internet Explorer & iFrames & Cookies)
Internet Explorer blocks cookies from iFrame pages, see the discussion on [Stack Overflow](http://stackoverflow.com/questions/389456/cookie-blocked-not-saved-in-iframe-in-internet-explorer).
You can fix this issue by adding P3P headers to your request. Use it with caution, because it might imply legal consequences (see the discussion on Stack Overflow for details).

You can use the listener provided by just setting in your config:

```yaml
becklyn_facebook:
    add_p3p_headers: true
```

It will add the following header to all responses:
`P3P: CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"`
