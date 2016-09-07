/**
 * Created by Michael on 20/7/16.
 */
var App = {
    me: {
        name: null,
        username: null
    }
};

function reloadPage() {
    location.reload();
}

function chatImg(src) {
    BootstrapDialog.show({
        title: "Attachment",
        message: '<img src="/media/' + src + '" alt="Attachment" style="width:100%;">',
    });
}


$(window).load(function() {

    $.ajax({
        url: '/ajax/me',
        dataType: 'JSON',
        success: function(data) {
            App.me = data.data;
        }
    });

    $("form.comment-form").submit(function(e) {
        e.preventDefault();

        var post = $(this).parents(".post-card");
        var table = post.find("table");
        table.append('<tr><td><a href="/users/'+App.me.username+'">'+App.me.name+'</a>: '+post.find("input[name=content]").val()+'</td></tr>');
        $.post({
            url: '/ajax/post_comment',
            method: "POST",
            data: $(this).serialize(),
            success: function(data) {
                post.find("input[name=content]").prop("disabled", false).val("");
            },
            beforeSend: function() {
                post.find("input[name=content]").prop("disabled", true);
            }
        })
    });

    $("form.new-post-form").submit(function(e) {
        e.preventDefault();
        $(this).find("input.btn[type=submit]").val('Please Wait').prop("disabled", true);
        //var formData = new FormData(this);
        $.ajax({
            url: '/ajax/new_post',
            method: 'POST',
            data: new FormData( this ),
            processData: false,
            contentType: false,
            success: function() {
                reloadPage();
            }
        });
    });

    // $("form.new-post-form input[type=file]").change(function(){
    //     var fname = $(this).val().split('\\').pop();
    //     console.log(fname);
    //     var button = $(this).parents(".btn");
    //     if(fname == "") {
    //         button.addClass("no-file-selected");
    //     } else {
    //         button.removeClass("no-file-selected");
    //         button.data("filename", fn)
    //     }
    // });

    $(".like-btn").click(function(e) {
        e.preventDefault();
        var btn = $(this);
        btn.attr("disabled", true);
        var postID = $(this).data("post-id");
        $.ajax({
            url: '/ajax/like_post/' + postID,
            method: 'POST',
            dataType: 'JSON',
            success: function(reponse) {
                btn.attr("disabled", false);
                if(reponse.success == true) {
                    if(reponse.data.liked == true) {
                        if(!btn.hasClass("already-liked")) {
                            btn.addClass("already-liked");
                        }
                        var likes = parseInt(btn.text().substr(1)) + 1;
                        console.log(btn.text().substr(1));
                        console.log(likes);
                        btn.html('<i class="fa fa-thumbs-up"></i> ' + likes);
                    } else {
                        btn.removeClass("already-liked");
                        var likes = parseInt(btn.text().substr(1)) - 1;
                        console.log(likes);
                        console.log(btn.text().substr(1));
                        btn.html('<i class="fa fa-thumbs-up"></i> ' + likes);
                    }
                }
            }
        })
    });

    $(".delete-post-btn").click(function(e) {
        e.preventDefault();
        var button = $(this);

        BootstrapDialog.confirm("Are you sure you want to delete that post", function(result) {
            if(result) {
                $.ajax({
                    url: '/ajax/delete_post/' + button.data("post-id"),
                    method: 'POST',
                    success: function() {
                        reloadPage();
                    }
                });
            }
        });
    });

    $(".btn-join-group").click(function(e) {
        e.preventDefault();

        button = $(this);

        $.ajax({
            url: '/ajax/join_group/' + button.data("group-id"),
            method: 'POST',
            success: function (data) {
                reloadPage();
            }
        });
    });
});