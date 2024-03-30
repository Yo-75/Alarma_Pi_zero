function DisplayError(titlu,mesaj) {
    $('<div id="tmp-x3"></div>').appendTo('body').html('<div id="dialog2" title="' + titlu + '">' +
        '<p> ' + mesaj + '</p></div>')
        .dialog({
            autoOpen: true,
            modal: true,
            resizable: false,
            title: titlu,
            height: "auto",
            width: 400,
            buttons: {
                "OK": function () {
                    $(this).dialog("close");
                    $("#tmp-x3").remove();
                }
            }
        });
}

//initializare tooltip
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});

$('.toggle').click(function(e) {
    e.preventDefault();

    const $this = $(this);

    if ($this.next().hasClass('show')) {
        $this.next().removeClass('show');
        // $this.next().slideUp(350);
    } else {
        $this.parent().parent().find('li .inner').removeClass('show');
        $this.parent().parent().find('li .inner').slideUp(350);
        $this.next().toggleClass('show');
        // $this.next().slideToggle(350);
    }
});
