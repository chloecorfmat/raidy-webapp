let blocks = document.querySelectorAll(".cards-block");

for(let block of blocks){
    let header = block.querySelector('header');

    header.addEventListener('click', function () {
        block.classList.toggle('open');
    });

}
