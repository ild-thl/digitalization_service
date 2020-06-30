require('./bootstrap');

$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ELMO Keys -------------------------------------------
    $(document).on('submit', '#newElmoKeyForm', function(e) {
        e.preventDefault();
        userFeedback('newElmoKeySubmitButton', 'loading');
        $('#newElmoKeyAlertError p').html('');
        $('#newElmoKeyAlertError').addClass('d-none');

        var newElmoKeyTitle = $('#newElmoKeyTitle').val();

        $.ajax({
            type: "POST",
            url: $('#newElmoKeyForm').attr('action'),
            data: {
                newElmoKeyTitle:newElmoKeyTitle,
            },
            success: function( json ) {
                try {
                    if(json.status == "success") {
                        userFeedback('newElmoKeySubmitButton', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        userFeedback('newElmoKeySubmitButton', 'error');
                        if(json.hasOwnProperty('errorMessage')) {
                            $('#newElmoKeyAlertError p').html(json.errorMessage);
                            $('#newElmoKeyAlertError').removeClass('d-none');
                        }
                        setTimeout(() => {
                            userFeedback('newElmoKeySubmitButton', 'text');
                        }, 1500);
                    }
                } catch (e) {
                    userFeedback('newElmoKeySubmitButton', 'error');
                    $('#newElmoKeyAlertError p').html(e);
                    $('#newElmoKeyAlertError').removeClass('d-none');
                    setTimeout(() => {
                        userFeedback('newElmoKeySubmitButton', 'text');
                    }, 1500);
                }
            },
            error: function (jqXHR, e) {
                userFeedback('newElmoKeySubmitButton', 'error');
                $('#newElmoKeyAlertError p').html(getAjaxError(jqXHR, e));
                $('#newElmoKeyAlertError').removeClass('d-none');
                setTimeout(() => {
                    userFeedback('newElmoKeySubmitButton', 'text');
                }, 1500);
            }
        });
    });

    $(document).on('hidden.bs.modal', '#newElmoKeyModal', function (e) {
        $('#newElmoKeyTitle').val('');
        $('#newElmoKeyAlertError p').html('');
        $('#newElmoKeyAlertError').addClass('d-none');
        userFeedback('newElmoKeySubmitButton', 'text');
    });

    $(document).on('show.bs.modal', '#deleteElmoKeyModal', function(event) {
        var button = $(event.relatedTarget);
        var elmokeyid = button.data('elmokeyid');
        var elmokeytitle = button.data('elmokeytitle');
        
        var modal = $(this);
        modal.find('.modal-body #deleteElmoKeyWarning em').text(elmokeytitle);
        modal.find('.modal-body #deleteElmoKeyId').val(elmokeyid);
    });

    $(document).on('submit', '#deleteElmoKeyForm', function(e) {
        var elmokeyid = $('.modal-body #deleteElmoKeyId').val();

        e.preventDefault();
        userFeedback('deleteElmoKeySubmitButton', 'loading');

        $.ajax({
            type: "POST",
            url: $('#deleteElmoKeyForm').attr('action'),
            data: {
                elmokeyid:elmokeyid,
            },
            success: function( json ) {
                try {
                    if(json.status == "success") {
                        userFeedback('deleteElmoKeySubmitButton', 'success');
                        setTimeout(() => {
                            if($('#row-elmokey-'+elmokeyid).parent().find('tr').length > 1) {
                                $('#row-elmokey-'+elmokeyid).remove();
                                $('#deleteElmoKeyModal').modal('hide');
                                userFeedback('deleteElmoKeySubmitButton', 'text');
                            } else {
                                window.location.reload();
                            }
                        }, 500);
                    } else {
                        userFeedback('deleteElmoKeySubmitButton', 'error');
                        if(json.hasOwnProperty('errorMessage')) {
                            $('#deleteElmoKeyAlertError p').html(json.errorMessage);
                            $('#deleteElmoKeyAlertError').removeClass('d-none');
                        }
                        setTimeout(() => {
                            userFeedback('deleteElmoKeySubmitButton', 'text');
                        }, 1500);
                    }
                } catch (e) {
                    userFeedback('deleteElmoKeySubmitButton', 'error');
                    $('#deleteElmoKeyAlertError p').html(e);
                    $('#deleteElmoKeyAlertError').removeClass('d-none');
                    setTimeout(() => {
                        userFeedback('deleteElmoKeySubmitButton', 'text');
                    }, 1500);
                }
            },
            error: function (jqXHR, e) {
                userFeedback('deleteElmoKeySubmitButton', 'error');
                $('#deleteElmoKeyAlertError p').html(getAjaxError(jqXHR, e));
                $('#deleteElmoKeyAlertError').removeClass('d-none');
                setTimeout(() => {
                    userFeedback('deleteElmoKeySubmitButton', 'text');
                }, 1500);
            }
        });
    });

    $(document).on('hidden.bs.modal', '#deleteElmoKeyModal', function (e) {
        $('#deleteElmoKeyWarning em').text('');
        $('#deleteElmoKeyId').val('');
        $('#deleteElmoKeyAlertError p').html('');
        $('#deleteElmoKeyAlertError').addClass('d-none');
        userFeedback('deleteElmoKeySubmitButton', 'text');
    });
    // ELMO Keys END -------------------------------------

    // Key Assignments -----------------------------------------------------
    $(document).on('show.bs.modal', '#deleteKeyAssignmentModal', function(event) {
        var button = $(event.relatedTarget);
        var keyassignmentid = button.data('keyassignmentid');
        var keyassignmenttag = button.data('keyassignmenttag');
        var elmokey = button.data('keyassignmentelmokey');
        
        var modal = $(this);
        modal.find('.modal-body #deleteKeyAssignmentWarning em').eq(0).text(keyassignmenttag);
        modal.find('.modal-body #deleteKeyAssignmentWarning em').eq(1).text(elmokey);
        modal.find('.modal-body #deleteKeyAssignmentId').val(keyassignmentid);
    });

    $(document).on('submit', '#deleteKeyAssignmentForm', function(e) {
        var keyassignmentid = $('.modal-body #deleteKeyAssignmentId').val();

        e.preventDefault();
        userFeedback('deleteKeyAssignmentSubmitButton', 'loading');

        $.ajax({
            type: "POST",
            url: $('#deleteKeyAssignmentForm').attr('action'),
            data: {
                keyassignmentid:keyassignmentid,
            },
            success: function( json ) {
                try {
                    if(json.status == "success") {
                        userFeedback('deleteKeyAssignmentSubmitButton', 'success');
                        setTimeout(() => {
                            if($('#row-keyassignment-'+keyassignmentid).parent().find('tr').length > 1) {
                                $('#row-keyassignment-'+keyassignmentid).remove();
                                $('#deleteKeyAssignmentModal').modal('hide');
                                userFeedback('deleteKeyAssignmentSubmitButton', 'text');
                            } else {
                                window.location.reload();
                            }
                        }, 500);
                    } else {
                        userFeedback('deleteKeyAssignmentSubmitButton', 'error');
                        if(json.hasOwnProperty('errorMessage')) {
                            $('#deleteKeyAssignmentAlertError p').html(json.errorMessage);
                            $('#deleteKeyAssignmentAlertError').removeClass('d-none');
                        }
                        setTimeout(() => {
                            userFeedback('deleteKeyAssignmentSubmitButton', 'text');
                        }, 1500);
                    }
                } catch (e) {
                    userFeedback('deleteKeyAssignmentSubmitButton', 'error');
                    $('#deleteKeyAssignmentAlertError p').html(e);
                    $('#deleteKeyAssignmentAlertError').removeClass('d-none');
                    setTimeout(() => {
                        userFeedback('deleteKeyAssignmentSubmitButton', 'text');
                    }, 1500);
                }
            },
            error: function (jqXHR, e) {
                userFeedback('deleteKeyAssignmentSubmitButton', 'error');
                $('#deleteKeyAssignmentAlertError p').html(getAjaxError(jqXHR, e));
                $('#deleteKeyAssignmentAlertError').removeClass('d-none');
                setTimeout(() => {
                    userFeedback('deleteKeyAssignmentSubmitButton', 'text');
                }, 1500);
            }
        });
    });

    $(document).on('hidden.bs.modal', '#deleteKeyAssignmentModal', function (e) {
        $('#deleteKeyAssignmentWarning em').text('');
        $('#deleteKeyAssignmentId').val('');
        $('#deleteKeyAssignmentAlertError p').html('');
        $('#deleteKeyAssignmentAlertError').addClass('d-none');
        userFeedback('deleteKeyAssignmentSubmitButton', 'text');
    });
    // Key Assignments END ------------------------------

    // Transformation -----------------------------------
    $(document).on('change', '#issuerTitle', function () {
        var issuerTitle = $('#issuerTitle').val().trim();
        if(issuerTitle == "") {
            $('#issuerTitle').addClass('is-invalid');
        } else {
            $('#issuerTitle').removeClass('is-invalid');
        }
    });

    $(document).on('change', '#xmlFile, #xmlText', function () {
        var xmlFile = $('#xmlFile').get(0).files;
        var xmlText = $('#xmlText').val().trim();
        
        if(xmlFile.length == 0 && xmlText == "") {
            $('#noXmlError').removeClass('d-none');
        } else  if(xmlFile.length == 1 && xmlText.length > 0) {
            $('#whichXmlError').removeClass('d-none');
        } else {
            $('#noXmlError').addClass('d-none');
            $('#whichXmlError').addClass('d-none');
            jQuery(this).removeClass('is-invalid');
        }
    });

    $(document).on('click', '#xmlFileClearButton', function() {
        $('#xmlFile').val('');
    });

    $(document).on('submit', '#transformStartForm', function() {
        var issuerTitle = $('#issuerTitle').val().trim();
        var xmlFile = $('#xmlFile').get(0).files;
        var xmlText = $('#xmlText').val().trim();

        if(issuerTitle == "" || (xmlFile.length == 0 && xmlText == "")) {
            if(xmlFile.length == 0 && xmlText == "") {
                $('#noXmlError').removeClass('d-none');
            }
            if(issuerTitle == "") {
                $('#issuerTitle').addClass('is-invalid');
            }
            return false;
        } else {
            if(xmlFile.length == 1 && xmlText.length > 0) {
                $('#whichXmlError').removeClass('d-none');
                return false;
            } else {
                userFeedback('startTransformSubmitButton', 'loading');
                return true;
            }
        }
    });

    $(document).on('change', '#transformAssignForm select', function (e) {
        if($(this).data('required') == true && $(this).val() == -1) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    $('#transformAssignForm').submit(function(ev) {
        return assignFormValidation(ev);
    });

    $(document).on('click', '#assignTransformSubmitButton', function (ev) {
        userFeedback('assignTransformSubmitButton', 'loading');
        if(assignFormValidation(ev)) {
            $('#transformAssignForm').submit();
        } else {
            userFeedback('assignTransformSubmitButton', 'error');
            setTimeout(() => {
                userFeedback('assignTransformSubmitButton', 'text');
            }, 1500);
        }
    });

    function assignFormValidation(ev) {
        var success = true;
        $('#transformAssignForm select').each(function(e) {
            if($(this).data('required') == true && $(this).val() == -1) {
                
                ev.preventDefault();
                $(this).addClass('is-invalid');
                if(success) {
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $(this).offset().top-30
                    }, 1000);
                }
                success = false;
            }
        });
        return success;
    }

    $(document).on('click', '#transformDownloadButton', function (e) {
        var path = $(this).data('zippath');

        if(path != "") {
            userFeedback('transformDownloadButton', 'loading');

            var xhr = new XMLHttpRequest();
			xhr.open('POST', $('#transformDownloadForm').attr('action'), true);
			xhr.responseType = 'arraybuffer';
			xhr.onload = function () {
					if (this.status === 200) {
						if(this.response != "") {
                            userFeedback('transformDownloadButton', 'success');
                            $('#transformDownloadButton').attr('disabled', 'disabled');

                            var filename = "";
							var disposition = xhr.getResponseHeader('Content-Disposition');
							if (disposition && disposition.indexOf('attachment') !== -1) {
                                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                                var matches = filenameRegex.exec(disposition);
                                if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
							}
							var type = xhr.getResponseHeader('Content-Type');

							var blob = typeof File === 'function'
									? new File([this.response], filename, { type: type })
									: new Blob([this.response], { type: type });
							if (typeof window.navigator.msSaveBlob !== 'undefined') {
                                // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
                                window.navigator.msSaveBlob(blob, filename);
							} else {
                                var URL = window.URL || window.webkitURL;
                                var downloadUrl = URL.createObjectURL(blob);

                                if (filename) {
                                    // use HTML5 a[download] attribute to specify filename
                                    var a = document.createElement("a");
                                    // safari doesn't support this yet
                                    if (typeof a.download === 'undefined') {
                                        window.location = downloadUrl;
                                    } else {
                                        a.href = downloadUrl;
                                        a.download = filename;
                                        document.body.appendChild(a);
                                        a.click();
                                    }
                                } else {
                                    window.location = downloadUrl;
                                }

                                setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 100); // cleanup
							}
						} else {
                            userFeedback('transformDownloadButton', 'error');
                            setTimeout(() => {
                                userFeedback('transformDownloadButton', 'text');
                            }, 1500);
                        }
					} else {
                        userFeedback('transformDownloadButton', 'error');
                        setTimeout(() => {
                            userFeedback('transformDownloadButton', 'text');
                        }, 1500);
                    }
			};
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
            xhr.send("path="+path);
            
        }
    });
    // Transformation END -------------------------------

    // Validate ------------------------------------------------
    $(document).on('click', '#elmoFileClearButton', function() {
        $('#elmoFile').val('');
    });

    $(document).on('click', '#startValidateSubmitButton', function () {
        userFeedback('startValidateSubmitButton', 'loading');
        $('#validateForm').submit();
    });
    // Validate END --------------------------------------------

    // Helper ------------------------
    function userFeedback(id, state) {
        if($.type(id) === "string") {
            var button = $('#'+id);
        } else if($.type(id) === "object") {
            var button = id;
        }
        var spans = button.find("span.userfeedback-text");
        if(spans.length == 0) {
            var text = button.html();
            button.css("width", button.outerWidth() +'px');
            button.css("height", button.outerHeight() +'px');
            button.html('<span class="userfeedback-text">'+text+'</span><span class="d-none userfeedback-loading"><i class="fas fa-spinner"></i></span><span class="d-none userfeedback-success"><i class="far fa-check-circle"></i></span><span class="d-none userfeedback-error"><i class="far fa-times-circle"></i></span>');
        }
        if(state == 'loading') {
            button.attr("disabled", "disabled");
            button.find("span").each(function()  {
                if($(this).hasClass('userfeedback-loading')) {
                    $(this).removeClass('d-none');
                } else {
                    $(this).addClass('d-none');
                }
            });
        }
        if(state == 'error') {
            button.removeAttr("disabled");
            button.find("span").each(function() {
                if($(this).hasClass('userfeedback-error')) {
                    $(this).removeClass('d-none');
                } else {
                    $(this).addClass('d-none');
                }
            });
        }
        if(state == 'success') {
            button.removeAttr("disabled");
            button.find("span").each(function() {
                if($(this).hasClass('userfeedback-success')) {
                    $(this).removeClass('d-none');
                } else {
                    $(this).addClass('d-none');
                }
            });
        }
        if(state == 'text') {
            button.removeAttr("disabled");
            button.find("span").each(function() {
                if($(this).hasClass('userfeedback-text')) {
                    $(this).removeClass('d-none');
                } else {
                    $(this).addClass('d-none');
                }
            });
        }
    }

    function getAjaxError(jqXHR, exception) {
        var msg = '';
        if (jqXHR.status === 0) {
            msg = 'Not connected.<br>Verify Network.';
        } else if (jqXHR.status == 404) {
            msg = 'Requested page not found [404]';
        } else if (jqXHR.status == 500) {
            msg = 'Internal Server Error [500]';
        } else if (exception === 'parsererror') {
            msg = 'Requested JSON parse failed';
        } else if (exception === 'timeout') {
            msg = 'Timed out';
        } else if (exception === 'abort') {
            msg = 'Ajax request aborted';
        } else {
            msg = 'Uncaught Error.<br>' + jqXHR.responseText;
        }
        return msg;
    }
    // Helper END -------------------------------------------
});