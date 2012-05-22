// Fix for console.log breaking IE
if(typeof console == 'undefined') {
    console = {};
    console.log = function() {};
    console.warn = function() {};
    console.error = function() {};
}


$(document).ready(function() {
    if(typeof qq != 'undefined') {
        var submitCallback = function(id, fileName) {
            $('.formatBox .save').animate({'padding-left':'26px'}, function() {
                $(this).prepend('<span class="loader"></span>');
                $(this).find('.text').text('Uploading...').siblings('.loader').fadeIn();
            });
        };
        
        var uploadedCallback = function(id, fileName, data) {
            $('.formatBox .save').stop();
            
            if(typeof data.success != 'undefined' && data.success) {
                var sizes = [];
                
                $('.formatBox table input:checked').each(function() {
                    sizes.push($(this).attr('value'));
                });
                
                $('.formatBox .save .text').text('Resizing...');
                
                $.getJSON('api/doResize', {'image': data.filename, 'name': data.orig_filename, 'sizes': sizes.join(',')}, function(response) {
                    if(response['status'] == 'success') {
                        
                    } else {
                        alert('Error: '+response['error']);
                    }
                    
                    $('.formatBox .save .loader').stop().fadeOut(function() {
                        $(this).parent().animate({'padding-left':'5px'});
                        $(this).siblings('.text').text('Resize Image[s]');
                        $(this).remove();
                    });
                });
            } else {
                $('.formatBox .save .loader').stop().fadeOut(function() {
                    $(this).parent().animate({'padding-left':'5px'});
                    $(this).siblings('.text').text('Resize Image[s]');
                    $(this).remove();
                });
                alert('Error: '+data['error']);
            }
        };
        
        var uploader = new qq.FileUploaderBasic({
            button: document.getElementById('fileUpload'),
            element: document.getElementById('fileUpload'),
            action: 'upload/do',
            multiple: false,
            allowedExtensions: ['jpg','jpeg','png','gif','bmp','pjpeg'],
            debug: true,
            onSubmit: submitCallback,
            onComplete: uploadedCallback,
            showMessage: function(message) {
                if(message == 'SIZE') {
                    setTimeout(function() {
                    }, 500);
                }
             }
        });
    }
});