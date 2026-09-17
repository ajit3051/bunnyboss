<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<!-- Back to Top Button -->
<button id="backToTop">
    <i class="fas fa-arrow-up"></i>
</button>

<style>
#backToTop{
    position:fixed;
    right:20px;
    bottom:20px;
    width:55px;
    height:55px;
    border:none;
    outline:none;
    border-radius:50%;
    cursor:pointer;
    z-index:9999;

    background:linear-gradient(135deg,#37475a,#37475a);
    color:#fff;
    font-size:22px;

    box-shadow:0 10px 30px rgba(255,106,0,.35);
    backdrop-filter:blur(10px);

    opacity:0;
    visibility:hidden;
    transform:translateY(20px);

    transition:.35s ease;
}

#backToTop:hover{
    transform:translateY(-6px) scale(1.08);
    box-shadow:0 15px 35px rgba(255,106,0,.5);
}

#backToTop.show{
    opacity:1;
    visibility:visible;
    transform:translateY(0);
}

#backToTop i{
    animation:floatArrow 1.2s infinite ease-in-out;
}

@keyframes floatArrow{
    0%,100%{
        transform:translateY(0);
    }
    50%{
        transform:translateY(-4px);
    }
}

/* Mobile */
@media(max-width:768px){
    #backToTop{
        width:48px;
        height:48px;
        right:15px;
        bottom:15px;
        font-size:18px;
    }
}
</style>

<script>
const backToTop = document.getElementById("backToTop");

window.addEventListener("scroll", function () {
    if (window.pageYOffset > 250) {
        backToTop.classList.add("show");
    } else {
        backToTop.classList.remove("show");
    }
});

backToTop.addEventListener("click", function () {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
});
</script>