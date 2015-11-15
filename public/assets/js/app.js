
var Form = function ($, selector, options)
{
    var me = this;
    var form = $(selector);
    var messages = {
        errors: {},
        success: ''
    };
    var data = new FormData();
    
    // Validate options
    var options = options || {};
    options.errorPrefix = typeof options.errorPrefix === 'undefined' ? '.v-error-' : options.errorPrefix;
    options.successPrefix = typeof options.successPrefix === 'undefined' ? '.v-error-' : options.successPrefix;
    if (typeof options.cb !== 'function') {
        options['cb'] = function (resp) {
            if (resp.redirect) {
                window.location = resp.redirect;
            }
        };
    }
    
    // Get first error message
    me.getErrorMessage = function (attr) {
        if (Object.keys(messages.errors).length) {
            if (typeof messages.errors[attr] === 'object') return messages.errors[attr][0];
            else return null;
        } else {
            return messages.success;
        }
    };
    
    // Get success message
    me.getSuccessMessage = function () {
        return messages.success;
    };
    
    // Hide messages
    me.hideMessages = function () {
        form.find(options.successPrefix).text(me.getSuccessMessage());
        $.each(messages.errors, function (attr) {
            form.find(options.errorPrefix + attr).text('');
        });
    };
    
    // Show debug messages
    me.displayDebugMessages = function (response) {
        if (console && console.log) {
            if (typeof response.errors === 'undefined') {
                console.log('Ajax response does not contains the errors object');
            }
            if (typeof response.success === 'undefined') {
                console.log('Ajax response does not contains the success string');
            }
            if (typeof response.redirect === 'undefined') {
                console.log('Ajax response does not contains the redirect string');
            }
        }
    };
    
    // Show validation messages
    me.display = function (response) {
        me.displayDebugMessages(response);
        me.hideMessages();
        messages.errors = (typeof response.errors === 'object' ? response.errors : {});
        messages.success = response.success;
        if (Object.keys(messages.errors).length) {
            $.each(messages.errors, function (attr) {
                form.find(options.errorPrefix + attr).text(me.getErrorMessage(attr));
            });
        } else {
            $(options.successPrefix).text(me.getSuccessMessage());
        }
    };
    
    // Capture pressed button
    form.find('[type="submit"]').click(function(){
        if ($(this).attr('name') && $(this).attr('value')) {
            form.append(
                $('<input type="hidden">').attr( { 
                    name: $(this).attr('name'), 
                    value: $(this).attr('value') })
            );
        }
    });
    
    // On submit
    form.on('submit', function (e) {
        e.preventDefault();
        data = new FormData();
        $.each(form.serializeArray(), function (i, item) {
            data.append(item.name, item.value);
        });
        
        // Include files
        if (options.files) {
            $.each(options.files, function(i, fileEl) {
                $.each($(fileEl)[0].files, function(i, file) {
                    data.append($(fileEl).attr('name') + '_' + i, file);
                });
            });
            
        }
        $.ajax({
            url: form.attr('action'),
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            success: function(resp){
                me.display(resp);
                options.cb(resp, me);
            }
        });
        return false;
    });
    
};

// http://phpjs.org/functions/strip_tags/
function strip_tags(input, allowed) {

  allowed = (((allowed || '') + '')
      .toLowerCase()
      .match(/<[a-z][a-z0-9]*>/g) || [])
    .join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
    commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
  return input.replace(commentsAndPhpTags, '')
    .replace(tags, function($0, $1) {
      return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    });
}