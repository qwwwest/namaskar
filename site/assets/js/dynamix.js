class Slider {
  constructor(elt) {
    this.slider = elt; //document.querySelector(query);
    this.slides = [...elt.querySelectorAll(".slides > *")];
    this.activeSlide = 0;

    this.indicators = [...elt.querySelectorAll(".indicators__item")];

    this.indicators.forEach((indicator, index) => {
      indicator.addEventListener("click", (e) => {
        this.setSlide(index);
      });
    });

    this.slider.addEventListener("mouseenter", () => {
      clearInterval(this.autoplayInterval);
    });
    this.slider.addEventListener("mouseleave", () => {
      this.autoPlay();
    });
    this.slider
      .querySelector("a.nextSlideButton")
      .addEventListener("click", (e) => {
        this.setSlide(this.activeSlide + 1);
      });
    this.slider
      .querySelector("a.prevSlideButton")
      .addEventListener("click", (e) => {
        this.setSlide(this.activeSlide - 1);
      });

    this.setSlide(this.activeSlide);
    this.autoPlay();
  }

  setSlide(num) {
    // or
    let oldSlide = this.activeSlide;
    animateCSS(this.slides[oldSlide], "out").then((message) => {
      this.slides[oldSlide].classList.remove("active");
      console.log(message);
    });

    this.indicators[this.activeSlide].classList.remove("active");

    this.activeSlide = (num + this.slides.length) % this.slides.length;

    this.slides[this.activeSlide].classList.add("active");
    this.indicators[this.activeSlide].classList.add("active");
    // animateCSS(this.slides[this.activeSlide], "scale-in-center");
  }

  autoPlay() {
    if (
      this.slider.dataset.autoplay === undefined ||
      this.slider.dataset.autoplay === "true"
    ) {
      this.autoplayInterval = setInterval(() => {
        this.setSlide(this.activeSlide + 1);
      }, this.slider.dataset.slideTime * 1);
    }
  }
}
animateCSS = (elt, animationName) =>
  // We create a Promise and return it
  new Promise((resolve, reject) => {
    elt.classList.add(animationName);

    // When the animation ends, we clean the classes and resolve the Promise
    function handleAnimationEnd() {
      elt.classList.remove(animationName);
      console.log("plop");
      elt.removeEventListener("animationend", handleAnimationEnd);

      resolve("Animation ended");
    }

    elt.addEventListener("animationend", handleAnimationEnd);
  });

//let sliders =

document.querySelectorAll(".slider").forEach((element) => {
  new Slider(element);
});

//TOC
(function () {
  let elt = document.querySelector(".toc");
  if (elt === null) return;
  let toc = "";
  headers = document.querySelectorAll("h2,h3,h4,h5,h6");
  for (let i = 0; i < headers.length; i++) {
    const h = headers[i];
    h.id = "toc-" + i;
    const tag = h.tagName;
    toc += `  <a href="#toc-${i}" class="${h.tagName}">${h.innerHTML}</a><br>\n `;
  }
  elt.innerHTML = toc;
})();
