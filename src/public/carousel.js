document.addEventListener("DOMContentLoaded", function () {
  const carouselElements = Array.from(document.querySelectorAll("[carousel]"));
  const carousels = carouselElements.map(function (c) {
    return new Carousel(c).setup();
  });
});

class Carousel {
  /** @type {Element}*/
  #element;
  #index = 0;
  #autoSlideInterval = 0;
  constructor(element) {
    this.#element = element;
  }

  setup() {
    this.previousBtn.addEventListener("click", () => {
      const totalSlides = this.totalSlides;
      this.#index = (this.#index - 1 + totalSlides) % totalSlides;
      this.update();
      this.startAutoSlide();
    });

    this.nextBtn.addEventListener("click", () => {
      const totalSlides = this.totalSlides;
      this.#index = (this.#index + 1) % totalSlides;
      this.update();
      this.startAutoSlide();
    });

    this.update();
    this.startAutoSlide();
    return this;
  }
  get nextBtn() {
    return document.querySelector("[carousel-next]");
  }

  get previousBtn() {
    return document.querySelector("[carousel-previous]");
  }

  get carouselImages() {
    return Array.from(
      this.#element.querySelector("[carousel-images]").children
    );
  }

  get carouselTitles() {
    return Array.from(
      this.#element.querySelector("[carousel-titles]").children
    );
  }

  get totalSlides() {
    return this.carouselTitles.length;
  }

  startAutoSlide() {
    const totalSlides = this.totalSlides;
    clearInterval(this.#autoSlideInterval);
    this.#autoSlideInterval = setInterval(() => {
      this.#index = (this.#index + 1) % totalSlides;
      this.update();
    }, 5000);
  }

  get carouselImagesContainer() {
    return this.#element.querySelector("[carousel-images]");
  }

  get carouselTitlesContainer() {
    return this.#element.querySelector();
  }

  update() {
    this.carouselImagesContainer.style.transform = `translateX(-${
      this.#index * 100
    }%)`;
    this.carouselTitles.forEach((title) => {
      title.classList.add("hidden");
    });

    this.carouselTitles[this.#index].classList.remove("hidden");
  }
}
