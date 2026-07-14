function showSection(id, el){

    // hide all sections
    document.querySelectorAll('.section').forEach(sec=>{
        sec.classList.remove('active');
    });

    document.getElementById(id).classList.add('active');

    // active menu
    document.querySelectorAll('.nav-links li').forEach(li=>{
        li.classList.remove('active');
    });

    el.classList.add('active');
}
