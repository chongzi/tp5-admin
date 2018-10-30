$(function(){
    // 保存与用户交互中可能会用到的值
    window.PageData = {
        loading:'<i class="fa fa-spin fa-refresh"></i>&nbsp; 执行中。。。',
        prompt:'',
        promptHander:null
    }

    window.alert = function(msg, type){
        var className = 'show main-alert alert alert-dismissible alert-',
            alert = $('#alert');

        type = typeof type =='undefined' ? 'danger' : type;
        className += type

        alert.find('span').html(msg)
        alert.attr('class', className)

        setTimeout(function(){
            closeAlert()
        }, 2000)
    }

    window.closeAlert = function(){
        $('#alert').removeClass('show')
    }

    var http = {
        get:function(url){
            $.ajax({
                url:url,
                type:'GET',
                dataType:'json',
                success:function(response){
                    if(response && response.code){
                        alert(response.msg, 'success')
                        if(response.url){
                            setTimeout(function(){
                                document.location.href = response.url
                            },2000)
                        }
                    }else{
                        alert(response.msg, 'danger')
                    }
                },
                error:function(response){
                    alert('请求错误', 'danger')
                }
            })
        },
        post:function(url, data, __callback){
            $.ajax({
                url:url,
                type:'POST',
                data:data,
                dataType:'json',
                success:function(response){
                    $('#modal-panel').modal('hide')
                    if(typeof __callback != 'undefined'){
                        __callback(response)
                    }else{

                        if(response && response.code){
                            alert(response.msg, 'success')
                            if(response.url){
                                setTimeout(function(){
                                    document.location.href = response.url
                                },2000)
                            }
                        }else{
                            alert(response.msg, 'danger')
                        }
                    }
                },
                error:function(response){
                    alert('请求错误', 'danger')
                }
            })
        },
        put:function(url, data, __callback){
            $.ajax({
                url:url,
                type:'PUT',
                data:data,
                dataType:'json',
                success:function(response){
                    $('#modal-panel').modal('hide')
                    if(typeof __callback != 'undefined'){
                        __callback(response)
                    }else{

                        if(response && response.code){
                            alert(response.msg, 'success')
                            if(response.url){
                                setTimeout(function(){
                                    document.location.href = response.url
                                },2000)
                            }
                        }else{
                            alert(response.msg, 'danger')
                        }
                    }
                },
                error:function(response){
                    alert('请求错误', 'danger')
                }
            })
        },
        delete:function(url){
            $.ajax({
                url:url,
                type:'DELETE',
                dataType:'json',
                success:function(response){
                    $('#modal-panel').modal('hide')
                    if(response && response.code){
                        location.reload()
                    }else{
                        alert(response.msg, 'danger')
                    }
                },
                error:function(response){
                    alert('请求错误', 'danger')
                }
            })
        }
    }
    window.httpClient = http;

    $('.back').on('click', function (e) {
        e.preventDefault()
        history.back()
    })

    // ajax-get
    $('.ajax-get').on('click', function(e){
        e.preventDefault();
        var url= $(this).attr('href');
        http.get(url)
    })

    // ajax post/put
    $('form.ajax-post').on('submit', function(e){
        e.preventDefault();
        var form = $(e.target), data, url, method, callback;
        callback = form.data('callback') ? window[form.data('callback')] : undefined;
        method = form.attr('method');
        if(form.attr('action')){
            url = form.attr('action')
            data = form.serialize()
        }
        if(url){
            //panel(loading)
            http[method.toLowerCase()](url, data, callback)
        }
    })
    $('a.ajax-post, button.ajax-post').on('click', function(e){
        e.preventDefault()
        var tar = $(this), form = tar.closest('form'), data, url, method = 'post', callback;
        url = tar.attr('href') ? tar.attr('href') : (tar.attr('url') ? tar.attr('url') : form.attr('action'));

        callback = form.data('callback') ? window[form.data('callback')] : undefined;
        if(form.lenght == 0){
            data = $('.input').serialize()
        }else{
            data = form.serialize()
        }
        if(url){
            //panel(loading)
            http[method.toLowerCase()](url, data, callback)
        }
    })

    // panel
    window.panel = function(content, title){
        if(typeof title != 'undefined'){
            $('#modal-panel .panel-heading').html(title)
        }
        $('#modal-panel .panel-body').html(content)
        $('#modal-panel').modal()
    }

    // ajax-delete
    var modalChoice = false;
    $('#modal-confirm .resolve').on('click', function(){
        modalChoice = true;
    })
    $('#modal-confirm .reject').on('click', function(){
        modalChoice = false;
    })
    $('.confirm, .ajax-delete').on('click', function(e){
        e.preventDefault()
        var option = {}, tar = $(this);
        option.url = tar.attr('href')
        new Promise((resolve, reject) => {
            $('#modal-confirm')
                .modal()
                .on('hidden.bs.modal', function (e) {
                    if(modalChoice){
                        resolve(option)
                    }else{
                        reject()
                    }
                })
        })
        .then(function(option){
            if(tar.hasClass('get')){
                http.get(option.url)
            }
            if(tar.hasClass('delete') || tar.hasClass('ajax-delete')){
                http.delete(option.url)
            }
        })
        .catch(function(){
            // 操作已取消
        });
    })

    // prompt
    $('#modal-prompt .resolve').on('click', function(){
        modalChoice = true;
    })
    $('#modal-prompt .reject').on('click', function(){
        modalChoice = false;
    })
    $('.prompt').on('click', function(e){
        e.preventDefault()
        var option = {}, tar = $(this), title;
        option.url = tar.attr('href')
        title = tar.data('title')
        callback = tar.data('callback') ? window[tar.data('callback')] : undefined;

        window.PageData.promptHander = new Promise((resolve, reject) => {
            $('#modal-prompt .modal-title').html(title)
            $('#modal-prompt')
                .modal()
                .on('hidden.bs.modal', function (e) {
                    if(modalChoice){
                        resolve(option)
                    }else{
                        reject()
                    }
                })
        })
        .then(function(option){
            var value = $('input[name=prompt]').val();
            window.PageData.prompt = value
            //panel(loading)
            http.post(option.url, {prompt:value}, callback)
            return Promise.resolve(option)
        })
        .catch(function(){
            // 操作已取消
        });
    })

    // checkbox
    $('input.check-all').change(function(e){
        var val = $(this).prop('checked')
        $('input.checkbox').prop('checked', val);
    })
    $('input.auth-checkbox').change(function(e){
        var tar = $(this), val = tar.prop('checked');
        if(tar.hasClass('main')){
            tar.closest('.box').find('input.auth-checkbox').prop('checked', val);
        }else{
            tar.closest('li').find('input.auth-checkbox').prop('checked', val);
        }
    })

    $('[data-toggle="tooltip"]').tooltip()

    $(document).on('keyup', function(e){
        e.preventDefault()
        if(e.keyCode == 13){
            $('form').submit()
        }
    })

    $('textarea').on('keyup', function(e){
        e.stopPropagation();
        return true;
    })

    window.UpdateImg = function(selector, uploader, _callback){
        if(window.FileReader){

            var id = 'Edgadw';
            var uploadHandler;
            var imgPre = $('<img id="pre"  width="0" style="visibility: hidden;position:absolute;" >');
            var reader = new FileReader();
            var active ;
            var picSrc;
            var rand = 0;
            var field;
            var file;

            $('body').append(imgPre);

            // 更换模板图片
            $(document).on('click', selector , function(e){
                field = $(selector).data('field-name')
                e.preventDefault();
                active = $(this);

                rand += 1;
                id += rand ;

                uploadHandler =  $('<input type="file" name="pic" id="'+id+'" class="hide" style="visibility: hidden" >');
                $('body').append(uploadHandler);

                $('#'+id).on('change',function(){
                    file = document.getElementById(id).files[0];

                    reader.readAsDataURL(file);
                    $('#'+id).remove(); // 解决选择图片与以上一次相同时，change不触发的问题
                })

                uploadHandler.trigger('click');
            });

            reader.onload =function(evt){
                imgPre.attr({src:evt.target.result});
                active.attr({src:evt.target.result});

                if(window.FormData){

                    var formData = new FormData();

                    // HTML 文件类型input，由用户选择
                    console.log(uploadHandler.val())
                    formData.append("image", file);

                    var request = new XMLHttpRequest();
                    request.open("POST", uploader);
                    request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                    request.setRequestHeader("accept", "application/json");
                    request.onreadystatechange = function(){
                        if(request.readyState == 4 && request.status == 200){
                            var resp = $.parseJSON(request.response)

                            if(field){
                                $('input[name='+field+']').val(resp.data)
                            }

                            if(_callback && typeof _callback == 'function'){
                                _callback(active,data)
                            }
                        }
                    }
                    request.send(formData);
                }else{
                    var data = active.data();

                    data['image'] = evt.target.result;

                    $.ajax({
                        url:uploader,
                        type:'post',
                        dataType:'json',
                        data:data,
                        success:function(data){
                            if(data && data.code){
                                if(field){
                                    $('input[name='+field+']').val(data.data)
                                }

                                if(_callback && typeof _callback == 'function'){
                                    _callback(active,data)
                                }
                            }
                        }
                    })
                }
            }
        }else{
            console.log("浏览器版本太低，不支持快捷换图！");
        };
    }

    UpdateImg('.image-upload', '/upload');

    $('input[data-type=password]').attr('type', 'password')
})
