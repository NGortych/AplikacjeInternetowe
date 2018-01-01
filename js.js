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
