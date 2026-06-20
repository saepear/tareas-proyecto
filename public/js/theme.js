var _lightColors = {1:'#4a8fe0',2:'#e8b830',3:'#00c896',4:'#9b6bff',5:'#ff6b81',6:'#ff9f43',7:'#6c7293',8:'#2ed8a0',9:'#e84393',10:'#54a0ff'};
var _darkColors  = {1:'#6aafe8',2:'#f0c850',3:'#2ed8a0',4:'#b084ff',5:'#ff6b81',6:'#ffb347',7:'#a8abb8',8:'#4ae0b0',9:'#f06292',10:'#74b9ff'};

function applyBubbleColor(idx) {
    var isDark = document.documentElement.classList.contains('dark');
    var color = (isDark ? _darkColors : _lightColors)[idx] || '#4a8fe0';
    document.querySelectorAll('.bubble').forEach(function(b) { b.style.background = color; });
}

function initTheme() {
    var p = window.matchMedia('(prefers-color-scheme:dark)').matches;
    if (p) {
        document.documentElement.classList.add('dark');
        if (document.body) document.body.classList.add('dark');
    }
    var c = localStorage.getItem('settingsSelectedColor');
    if (c) {
        var m = {1:'ocean',2:'sunset',3:'forest',4:'lavender',5:'rose',6:'amber',7:'slate',8:'teal',9:'berry',10:'sky'};
        var n = m[c] || c;
        document.documentElement.setAttribute('data-theme', n);
        if (document.body) document.body.classList.add('theme-' + n);
        applyBubbleColor(c);
    }
    var ns = localStorage.getItem('settingsSelectedNavStyle');
    if (ns && ns !== 'none') document.documentElement.setAttribute('data-nav-style', ns);
    var font = localStorage.getItem('settingsSelectedFont');
    if (font) {
        var fonts = { moderna:'Inter, -apple-system, BlinkMacSystemFont, sans-serif', tech:'JetBrains Mono, Cascadia Code, Fira Code, monospace', elegante:'Playfair Display, Georgia, serif', clasica:'Georgia, Times New Roman, serif' };
        if (fonts[font] && document.body) document.body.style.fontFamily = fonts[font];
    }
}
