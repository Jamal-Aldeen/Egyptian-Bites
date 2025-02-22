<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="container py-5">
    <div class="row">
        <div class="col-sm-12">
            <h1 class="text-center">Change Password</h1>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-sm-6">
            <p class="text-center">Use the form below to change your password. Your password cannot be the same as your username.</p>
            <form method="POST" id="passwordForm" action="password-reset-code.php">
                <div class="mb-3">
                    <input type="password" class="form-control form-control-lg" name="password1" id="password1" placeholder="New Password" autocomplete="off">
                    <div class="row">
                        <div class="col-sm-6">
                            <span id="8char" class="glyphicon-remove" style="color:#FF0004;"></span> 8 Characters Long<br>
                            <span id="ucase" class="glyphicon-remove" style="color:#FF0004;"></span> One Uppercase Letter
                        </div>
                        <div class="col-sm-6">
                            <span id="lcase" class="glyphicon-remove" style="color:#FF0004;"></span> One Lowercase Letter<br>
                            <span id="num" class="glyphicon-remove" style="color:#FF0004;"></span> One Number
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control form-control-lg" name="password2" id="password2" placeholder="Repeat Password" autocomplete="off">
                    <div class="row">
                        <div class="col-sm-12">
                            <span id="pwmatch" class="glyphicon-remove" style="color:#FF0004;"></span> Passwords Match
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="submit" class="btn btn-primary btn-lg w-100" data-loading-text="Changing Password..." value="Change Password">
                </div>
            </form>
        </div><!--/col-sm-6-->
    </div><!--/row-->
</div>

<script>
    $(document).ready(function () {
        // Validate Password Strength
        $('#password1').on('keyup', function () {
            var password = $(this).val();
            
            // Check password length
            if (password.length >= 8) {
                $('#8char').removeClass('glyphicon-remove').addClass('glyphicon-ok').css("color", "#4F8A10");
            } else {
                $('#8char').removeClass('glyphicon-ok').addClass('glyphicon-remove').css("color", "#FF0004");
            }

            // Check for uppercase letter
            if (/[A-Z]/.test(password)) {
                $('#ucase').removeClass('glyphicon-remove').addClass('glyphicon-ok').css("color", "#4F8A10");
            } else {
                $('#ucase').removeClass('glyphicon-ok').addClass('glyphicon-remove').css("color", "#FF0004");
            }

            // Check for lowercase letter
            if (/[a-z]/.test(password)) {
                $('#lcase').removeClass('glyphicon-remove').addClass('glyphicon-ok').css("color", "#4F8A10");
            } else {
                $('#lcase').removeClass('glyphicon-ok').addClass('glyphicon-remove').css("color", "#FF0004");
            }

            // Check for a number
            if (/\d/.test(password)) {
                $('#num').removeClass('glyphicon-remove').addClass('glyphicon-ok').css("color", "#4F8A10");
            } else {
                $('#num').removeClass('glyphicon-ok').addClass('glyphicon-remove').css("color", "#FF0004");
            }
        });

        // Check if passwords match
        $('#password2').on('keyup', function () {
            var password1 = $('#password1').val();
            var password2 = $(this).val();
            
            if (password1 === password2) {
                $('#pwmatch').removeClass('glyphicon-remove').addClass('glyphicon-ok').css("color", "#4F8A10");
            } else {
                $('#pwmatch').removeClass('glyphicon-ok').addClass('glyphicon-remove').css("color", "#FF0004");
            }
        });
    });
</script>
