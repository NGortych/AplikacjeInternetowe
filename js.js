//rejestracja - zdjecie zarazpo wczytaniu
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#blah')
                    .attr('src', e.target.result)
                    .width(300)
                    .height(400);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function displayFields() {
    var type = $("#type_of_user").val();
    if (type === "Student") {
        $("#department").hide();
        $("#study").show();
        $("#indexNM").show();
    } else if (type === "Nauczyciel") {
        $("#department").show();
        $("#study").hide();
        $("#indexNM").hide();
    }
}


$(function () {

    $.validator.addMethod("regex", function (value, element, regexpr) {
        return regexpr.test(value);
    }, "Nieprawidłowa wartość");
    $("#modalForm").validate({
        errorClass: "validate_error_class",
        rules: {
            emailM: {
                required: true,
                email: true
            },
            passwordM: {
                minlength: 8,
                maxlength: 20,
            },

            password_confirmM: {
                equalTo: "#passwordM"
            },
            nameM: {
                required: true,
                minlength: 3,
                maxlength: 20,
                regex: /^[A-ZŁŚ]{1}[a-ząęśżźćń]+$/
            },
            surnameM: {
                minlength: 3,
                maxlength: 20,
                regex: /^[A-ZŁŚ]{1}[a-ząęśżźćń]+$/
            },
            index_NM: {
                minlength: 6,
                maxlength: 6,
                regex: /^[0-9]+$/
            },

        },
        messages: {
            emailM: {
                required: "Podaj swój email.",
                email: "Niepoprawny format email'a."
            },
            passwordM: {
                minlength: "Hasło musi składać sie z minimum 8 znaków",
                maxlength: "Hasło może składać sie z maksimum 20 znaków",
                
            },
            password_confirmM: {
                minlength: "Hasło musi składać sie z minimum 8 znaków",
                maxlength: "Hasło może składać sie z maksimum 20 znaków",
                equalTo: "Wprowadzone hasła nie są takie "
            },
            nameM: {
                minlength: "Imię musi składać sie z minimum 3 znaków",
                maxlength: "Imię może składać sie z maksimum 20 znaków",
                regex: "Wprowadzone imię jest niepoprawne"
            },
            surnameM: {
                minlength: "Nazwisko musi składać sie z minimum 3 znaków",
                maxlength: "Nazwisko może składać sie z maksimum 20 znaków",
                regex: "Wprowadzone nazwisko jest niepoprawne"
            },
            index_NM: {
                minlength: "Numer indeksu musi składac się z dokładnie 6 cyfr",
                maxlength: "Numer indeksu musi składac się z dokładnie 6 cyfr",
                regex: "Numer indeksu składa się tylko z cyfr"
            },

        }
    });
});