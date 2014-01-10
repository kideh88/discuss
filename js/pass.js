var regExp = /^(?=(?:[^A-Z]*[A-Z]){2,})(?=(?:[^a-z]*[a-z]){2,})(?=(?:[^\d]*[\d]){2})(?=(?:[^\W]*[\W]){2})[A-Za-z\d\W]{8,}$/,
                    lowerCase = /(?=(?:[^a-z]*[a-z]){2,})/,
                    upperCase = /(?=(?:[^A-Z]*[A-Z]){2,})/,
                    numbers = /(?=(?:[^\d]*[\d]){2})/,
                    specialChars = /(?=(?:[^\W]*[\W]){2})/;
            // Paâ‚¬sSwo8rd!9 -> valid password

            $(function() {
                var $input = $("#RegisterPassword"),
                        $result = $("#result"),
                        strongPass = false,
                        $registrationform = $("#registrationform");

                $input.on("blur change keydown keyup keypress focus", function() {
                    if (regExp.test($input.val())) {
                        $result.text("true");
                        strongPass = true;
                    } else {
                        $result.text("false");
                        strongPass = false;
                    }
                    
                    errMsg();
                });
                
                $registrationform.submit(function(e) {
                    if (!strongPass) {
                        e.preventDefault();
                    }
                });
                
            });

            function errMsg() {
                var $input = $("#RegisterPassword");

                if (!lowerCase.test($input.val())) {
                    $("#lowercase").show();
                } else {
                    $("#lowercase").hide();
                }

                if (!upperCase.test($input.val())) {
                    $("#uppercase").show();
                } else {
                    $("#uppercase").hide();
                }

                if (!numbers.test($input.val())) {
                    $("#numbers").show();
                } else {
                    $("#numbers").hide();
                }

                if (!specialChars.test($input.val())) {
                    $("#specialchars").show();
                } else {
                    $("#specialchars").hide();
                }

                if ($("#RegisterPassword").val().length < 8) {
                    $("#length").show();
                } else {
                    $("#length").hide();
                }
            }