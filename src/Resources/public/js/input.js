$('input[type="file"]').on("change", function (evt) {
    evt.target.labels.forEach(function (v) {
        let label = $(v).prop("class");
        if ('custom-file-label' == label) {
            $(v).html(evt.target.files[0].name);
        }
    });
});