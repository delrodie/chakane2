const imgs = document.querySelectorAll('.img-select a');
const imgBtns = [...imgs];
let imgId = 1;

imgBtns.forEach((imgItem) => {
    imgItem.addEventListener('click', (event) => {
        event.preventDefault();
        imgId = imgItem.dataset.id;
        slideImage();
    });
});

function slideImage(){
    const displayWidth = document.querySelector('.img-showcase img:first-child').clientWidth;

    document.querySelector('.img-showcase').style.transform = `translateX(${- (imgId - 1) * displayWidth}px)`;
}

window.addEventListener('resize', slideImage);


new VenoBox({
    selector: '.img-link',
    numeration: true,
    infinigall: true,
    share: true,
    spinner: 'rotating-plane',
    maxWidth: "50%"
});

function zoom(smallImg){
    var fullImg = document.getElementById('photoBox');
    //var dataHref = document.
    fullImg.src = smallImg.src;
    fullImg.setAttribute('data-href', smallImg.src);
}

