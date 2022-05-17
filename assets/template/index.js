function saveToPng() {
    html2canvas(document.getElementById("code-block"), {backgroundColor: null}).then(function (canvas) {
        var anchor = document.createElement("a");
        anchor.href = canvas.toDataURL("image/png");
        anchor.download = document.title + ".png";
        anchor.click();
    });
}

class ObfuscatedSpan extends HTMLElement {
    constructor() {
        super();

        const shadow = this.attachShadow({mode: 'open'});
        const text = document.createElement('span');
        shadow.appendChild(text);

        setInterval(function () {
            text.textContent = String.fromCharCode(Math.floor(Math.random() * (95 - 64 + 1)) + 64);
        }, 50);
    }
}

customElements.define('o-s', ObfuscatedSpan);