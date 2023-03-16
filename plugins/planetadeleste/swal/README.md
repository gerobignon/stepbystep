# Installation	{#installation}
From `Setting / System / Updates` panel, click on `Install plugins` button and write `PlanetaDelEste.Swal`.

* * *

## Important
After Swal `1.1.0` and OC build `397`, ajax alerts require the data attribute `data-request-flash` ( [Flash messages](http://octobercms.com/docs/ajax/extras#ajax-flash) ) 
 to see Flash messages without reload the page. 
 
If `data-validate-error` are present, Swal doesn't show the alert.

The library `$.oc.flashMsg` was replaced with `$.flashSwal`

* * *

# Custom Flash alerts	{#custom-flash-alerts}
![Setting panel](https://box.everhelper.me/attachment/293046/r7lzpnvLFnkALhTZaxr2E4PptDrX4Yrf/397815-mAg2s6TdXHyaVDsp/screenshot-swal-config-1.png)
You can customize the alert messages, creating custom settings.
From `Settings / CMS / Sweet Alert`, you can create your own configuration. Read the [sweetAlert](http://t4t5.github.io/sweetalert/) documentation for setting options.

* * *

## Configuration	{#sweetalert-configuration}
#### Here are the keys that you can use if you pass an object into [sweetAlert](http://t4t5.github.io/sweetalert/):

| Argument | Default value | Description |
|-------------|----------------|--------------|
| **title** | `null (required)` | The title of the modal. It can either be added to the object under the key "title" or passed as the first parameter of the function. |
| **text** | `null` | A description for the modal. It can either be added to the object under the key "text" or passed as the second parameter of the function. |
| **type** | `null` | The type of the modal. SweetAlert comes with 4 built-in types which will show a corresponding icon animation: "**warning**", "**error**", "**success**" and "**info"**. You can also set it as "**input**" to get a prompt modal. It can either be put in the object under the key "type" or passed as the third parameter of the function. |
| **allowEscapeKey** | `true` | If set to **true**, the user can dismiss the modal by pressing the Escape key. |
| **customClass** | `null` | A custom CSS class for the modal. It can be added to the object under the key "customClass". |
| **allowOutsideClick** | `false` | If set to **true**, the user can dismiss the modal by clicking outside it. |
| **showCancelButton** | `false` | If set to **true**, a "Cancel"-button will be shown, which the user can click on to dismiss the modal. |
| **showConfirmButton** | `true` | If set to **false**, the "OK/Confirm"-button will be hidden. Make sure you set a timer or set allowOutsideClick to true when using this, in order not to annoy the user. |
| **confirmButtonText** | `OK` | Use this to change the text on the "Confirm"-button. If showCancelButton is set as true, the confirm button will automatically show "Confirm" instead of "OK". |
| **confirmButtonColor** | `#AEDEF4` | Use this to change the background color of the "Confirm"-button (must be a HEX value). |
| **cancelButtonText** | `Cancel` | Use this to change the text on the "Cancel"-button. |
| **closeOnConfirm** | `true` | Set to _false_ if you want the modal to stay open even if the user presses the "Confirm"-button. This is especially useful if the function attached to the "Confirm"-button is another SweetAlert. |
| **closeOnCancel** | `true` | Same as closeOnConfirm, but for the cancel button. |
| **imageUrl** | `null` | Add a customized icon for the modal. Should contain a string with the path to the image. |
| **imageSize** | `80x80` | If imageUrl is set, you can specify imageSize to describes how big you want the icon to be in px. Pass in a string with two values separated by an "x". The first value is the width, the second is the height. |
| **timer** | `null` | Auto close timer of the modal. Set in ms (milliseconds). |
| **html** | `false` | If set to true, will not escape title and text parameters. (Set to false if you're worried about XSS attacks.) |
| **animation** | `true` | If set to **false**, the modal's animation will be disabled. Possible (string) values : **pop** (default when animation set to true), **slide-from-top**, **slide-from-bottom** |
| **inputType** | `text` | Change the type of the input field when using **type: "input"** (this can be useful if you want users to type in their password for example). |
| **inputPlaceholder** | `null` | When using the input-type, you can specify a placeholder to help the user. |
| **inputValue** | `null` | Specify a default text value that you want your input to show when using **type: "input"** |
| **showLoaderOnConfirm** | `false` | Set to **true** to disable the buttons and show that something is loading. |

* * *

# Using the component	{#using-component}
![Component](https://box.everhelper.me/attachment/295348/r7lzpnvLFnkALhTZaxr2E4PptDrX4Yrf/397815-BGXcEXKmcyzMC8mP/screenshot-swal-component-config.png)
The component have only one optional setting. Select the configuration options.
You can add the component to `layout` for a global use.

## Notes!
The component add the styles and javascript files. Require `jQuery`. Is important to attach the component in the page or layout with the markup `{% component 'sweetAlertMessage' %}`. This can be outside html tags.

    {% component 'sweetAlertMessage' %}
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <title>October CMS - {{ this.page.title }}</title>

**Styles added:**

	// Added in {% styles %}
	<link rel="stylesheet" href="/plugins/planetadeleste/swal/assets/vendor/sweetalert/sweetalert.css">
	
**Scripts added:**

	// Added in {% scripts %}
	<script src="/plugins/planetadeleste/swal/assets/vendor/sweetalert/sweetalert.min.js"></script>
	<script src="/plugins/planetadeleste/swal/assets/js/flash-swal.js"></script>