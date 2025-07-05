
/*********************************
* ghost-hamburger-menu
*********************************/
const hamburger = document.querySelector('.ghost-hamburger');
const nav = document.querySelector('.ghost-nav');
//const hamburger = document.getElementById('jsGhostHamburger');

//const nav = document.getElementById('jsGhostNav');
hamburger.addEventListener('click', function () {
  hamburger.classList.toggle('active');
  nav.classList.toggle('active');
});

/*********************************
* ghost-tab
*********************************/
const tabItem    = document.querySelectorAll(".ghost-tab-item");
const tabContent = document.querySelectorAll(".ghost-tab-content");

for (let i = 0; i < tabItem.length; i++) {
  tabItem[i].addEventListener("click", tabToggle);
}

function tabToggle() {
  for (let i = 0; i < tabItem.length; i++) {
    tabItem[i].classList.remove("active");
  }
  for (let i = 0; i < tabContent.length; i++) {
    tabContent[i].classList.remove("active");
  }
  this.classList.add("active");

  const aryTabs = Array.prototype.slice.call(tabItem);

  const index = aryTabs.indexOf(this);

  tabContent[index].classList.add("active");
}
/*********************************
* Modal
*********************************/
const btn = document.querySelector('.ghost-modal-btn');
const modal = document.querySelector('.ghost-modal');
const closeBtn = document.querySelector('.ghost-modal-close');
const overlay = document.querySelector('.ghost-overlay');

btn.addEventListener('click', function(e){
  e.preventDefault();
  modal.classList.add('active');
  overlay.classList.add('active');
});

closeBtn.addEventListener('click', function(){
  modal.classList.remove('active');
  overlay.classList.remove('active');
});

ghost-overlay.addEventListener('click', function() {
  modal.classList.remove('active');
  overlay.classList.remove('active');
});
