  var wysiwyg_images_upload_Options = {
    customTemplates: {
      /* this is the template for the image button in the toolbar */
      "image": function(locale, options) {
            var size = (options && options.size) ? ' btn-'+options.size : '';
            return "<li>" +
              "<div class='bootstrap-wysihtml5-insert-image-modal modal fade'>" +
              ($.fn.modalmanager ? "" : "<div class='modal-dialog'>") +
              " <div class='modal-content'>" +
                "<div class='modal-header'>" +
                  "<a class='close' data-dismiss='modal'>&times;</a>" +
                  "<h3>" + locale.image.insert + "</h3>" +
                "</div>" +
                "<div class='modal-body fileupload_wysihtml5'>" +
                  "<input value='http://' autocomplete='off' class='bootstrap-wysihtml5-insert-image-url form-control input-xlarge fileupload_wysihtml5_imgurlinput'>" +
                  "<div class='fileupload_img margin-top-10'>"+
                        "<img src='"+WPF.files_hosts+"/img/common/no_img_80x80.png' width='80' height='80'/>"+                                               
                  "</div>"+
                  "<div class='margin-top-10'>" +
                        "<span class='btn btn-success fileinput-button'>"+
                            "<i class='glyphicon glyphicon-plus'></i>"+
                            "<span>上传...</span>"+
                            "<input class='fileupload_wysihtml5_input' type='file' accept='image/*' autocomplete='off' />"+
                        "</span>"+
                  "</div>"+
                  "<div class='progress fileupload_progress margin-top-10' style='display: none;'>"+
                        "<div class='progress-bar progress-bar-success'></div>"+
                  "</div>"+
                "</div>" +
                "<div class='modal-footer'>" +
                  "<a href='#' class='btn default' data-dismiss='modal'>" + locale.image.cancel + "</a>" +
                  "<a href='#' class='btn btn-primary' data-dismiss='modal'>" + locale.image.insert + "</a>" +
                "</div>" +
              "</div>" +
               ($.fn.modalmanager ? "" : "</div>") +
              "</div>" +
              "<a class='btn default" + size + "' data-wysihtml5-command='insertImage' title='" + locale.image.insert + "' tabindex='-1'><i class='fa fa-picture-o'></i></a>" +
            "</li>";
      }      
    }
  };
