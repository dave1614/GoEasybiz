<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        {{-- <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap"> --}}
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css"
            integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
        <link href="{{ asset('/css/treeflex.css') }}" rel="stylesheet">
        <!-- Scripts -->
        @routes
        @vite('resources/js/app.js')
        @inertiaHead
        <script>
        function submitSearchMlmInput (elem,evt) {
            evt.preventDefault();
            var me = $(elem);
            
            $(".overlay").show();
            // $("#main-page-col-md-12").hide();
            var sear_phone = me.find("#search-mlm-input").val();
            var form_data = {
            _token : "<?php echo csrf_token(); ?>",
            show_records : true,
            phone: sear_phone
            }
            var url = "/search_users_genealogy_tree";
            $.ajax({
                url : url,
                type : "POST",
                responseType : "json",
                dataType : "json",
                data : form_data,
                success : function (response) {
                    $(".overlay").hide();
                    // $("#main-page-col-md-12").show();
                    if(response.success && response.messages != ""){
                        var messages = response.messages;
                        
                        
                        $("#main-page-col-md-12").html(messages);
                        
                        // $('img').bind('contextmenu', function(e) {
                        // return false;
                        // });
                        window.scrollTo(0, 0);
                    }else if(response.invalid_user){
                        Swal.fire({
                            title: 'Ooops',
                            text: "This User Name Is Invalid.",
                            icon: 'warning'
                        })
                    }else{
                        Swal.fire({
                            title: 'Ooops',
                            text: "Something Went Wrong",
                            icon: 'warning'
                        })
                    }
                },error : function () {
                    $(".overlay").hide();
                    // $("#main-page-col-md-12").show();
                    Swal.fire({
                        title: 'Ooops',
                        text: "Something Went Wrong",
                        icon: 'error'
                    })
                },complete: function(xhr, textStatus) {
                    console.log(xhr.status);
                    if(xhr.status == 419){
                        document.location.reload()
                    }
                }
            });
        }
        
        function clearSearchInputTextField (elem,evt) {
            $("#search-mlm-form #search-mlm-input").val("");
        }
        function goDownMlm(elem,event,mlm_db_id,your_mlm_db_id){
            elem = $(elem);
            var package = elem.attr("data-package");
            $(".overlay").show();
            // $("#main-page-col-md-12").hide();
            var form_data = {
            _token : "<?php echo csrf_token(); ?>",
            mlm_db_id : mlm_db_id,
            your_mlm_db_id : your_mlm_db_id,
            package : package
            }
            var url = "/view_your_genealogy_tree_down"
            $.ajax({
                url : url,
                type : "POST",
                responseType : "json",
                dataType : "json",
                data : form_data,
                success : function (response) {
                    $(".overlay").hide();
                    // $("#main-page-col-md-12").show();
                    if(response.success && response.messages != ""){
                        var messages = response.messages;
                        
                        // $('img').bind('contextmenu', function(e) {
                        // return false;
                        // });
                        $("#main-page-col-md-12").html(messages);
                        
                        window.scrollTo(0, 0);
                    }else{
                        Swal.fire({
                            title: 'Ooops',
                            text: "Something Went Wrong",
                            icon: 'warning'
                        })
                    }
                },error : function () {
                    $(".overlay").hide();
                    // $("#main-page-col-md-12").show();
                    Swal.fire({
                        title: 'Ooops',
                        text: "Something Went Wrong",
                        icon: 'error'
                    })
                },
                complete: function(xhr, textStatus) {
                    console.log(xhr.status);
                    if(xhr.status == 419){
                        document.location.reload()
                    }
                }
            });
        }
        
        function goUpMlm(elem,event,mlm_db_id,your_mlm_db_id){
            elem = $(elem);
            var package = elem.attr("data-package");
            $(".overlay").show();
            var form_data = {
            _token : "<?php echo csrf_token(); ?>",
            show_records : true,
            mlm_db_id : your_mlm_db_id,
            package : package
            }
            var url = "/view_your_genealogy_tree"
            $.ajax({
                url : url,
                type : "POST",
                responseType : "json",
                dataType : "json",
                data : form_data,
                success : function (response) {
                    $(".overlay").hide();
                    if(response.success && response.messages != ""){
                        var messages = response.messages;
                        
                        // $('img').bind('contextmenu', function(e) {
                        // return false;
                        // });
                        $("#main-page-col-md-12").html(messages);
                        
                        window.scrollTo(0, 0);
                    }else{
                        Swal.fire({
                            title: 'Ooops',
                            text: "Something Went Wrong",
                            icon: 'warning'
                        })
                    }
                },error : function () {
                    $(".overlay").hide();
                    Swal.fire({
                        title: 'Ooops',
                        text: "Something Went Wrong",
                        icon: 'error'
                    })
                },
                complete: function(xhr, textStatus) {
                    console.log(xhr.status);
                    if(xhr.status == 419){
                        document.location.reload()
                    }
                }
            });
        }
        </script>
    </head>
    <body class="font-sans antialiased">
        @inertia
        
    </body>
    <script src="{{ asset('/js/sweet_alert2.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</html>
