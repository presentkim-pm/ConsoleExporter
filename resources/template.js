window.onload = () => {
    document.getElementById("controls").addEventListener("click", () => {
        html2canvas(document.getElementById("code-block"), {backgroundColor: null}).then(function (canvas) {
            var anchor = document.createElement("a");
            anchor.href = canvas.toDataURL("image/png");
            anchor.download = document.title + ".png";
            anchor.click();
        });
    });

    setInterval(function () {
        for (var span of document.body.getElementsByClassName("tk")) {
            span.textContent = String.fromCharCode(Math.floor(Math.random() * (95 - 64 + 1)) + 64);
        }
    }, 50);
};
