
(function () {
  "use strict";

  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all)
    if (selectEl) {
      if (all) {
        selectEl.forEach(e => e.addEventListener(type, listener))
      } else {
        selectEl.addEventListener(type, listener)
      }
    }
  }

  /**
   * Easy on scroll event listener 
   */
  const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
  }

  /**
   * Navbar links active state on scroll
   */

  const scrollto = (el) => {
    let header = select('#header')
    let offset = header.offsetHeight

    if (!header.classList.contains('header-scrolled')) {
      offset -= 16
    }

    let elementPos = select(el).offsetTop
    window.scrollTo({
      top: elementPos - offset,
      behavior: 'smooth'
    })
  }


  // let selectHeader = select('#header')
  // if (selectHeader) {
  //   let headerOffset = selectHeader.offsetTop
  //   let nextElement = selectHeader.nextElementSibling
  //   const headerFixed = () => {
  //     if ((headerOffset - window.scrollY) <= 0) {
  //       selectHeader.classList.add('fixed-top')
  //       nextElement.classList.add('scrolled-offset')
  //     } else {
  //       selectHeader.classList.remove('fixed-top')
  //       nextElement.classList.remove('scrolled-offset')
  //     }
  //   }
  //   window.addEventListener('load', headerFixed)
  //   onscroll(document, headerFixed)
  // }

  let backtotop = select('.whatsapp')
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add('active')
      } else {
        backtotop.classList.remove('active')
      }
    }
    window.addEventListener('load', toggleBacktotop)
    onscroll(document, toggleBacktotop)
  }


  /**
   * Scroll with ofset on page load with hash links in the url
   */
  window.addEventListener('load', () => {
    if (window.location.hash) {
      if (select(window.location.hash)) {
        scrollto(window.location.hash)
      }
    }
  });

  /**
   * Preloader
   */
  let preloader = select('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove()
    });
  }

  /**
   * Porfolio isotope and filter
   */
  window.addEventListener('load', () => {
    let portfolioContainer = select('.portfolio-container');
    if (portfolioContainer) {
      let portfolioIsotope = new Isotope(portfolioContainer, {
        itemSelector: '.portfolio-item'
      });

      let portfolioFilters = select('#portfolio-flters li', true);

      on('click', '#portfolio-flters li', function (e) {
        e.preventDefault();
        portfolioFilters.forEach(function (el) {
          el.classList.remove('filter-active');
        });
        this.classList.add('filter-active');

        portfolioIsotope.arrange({
          filter: this.getAttribute('data-filter')
        });
        portfolioIsotope.on('arrangeComplete', function () {
          AOS.refresh()
        });
      }, true);
    }

  });
  let deadline = new Date("Sep 30, 2023 09:00:00")
    .getTime();

  // To call defined fuction every second
  let x = setInterval(function () {

    // Getting current time in required format
    let now = new Date().getTime();

    // Calculating the difference
    let t = deadline - now;

    // Getting value of days, hours, minutes, seconds
    let days = Math.floor(t / (1000 * 60 * 60 * 24));
    let hours = Math.floor(
      (t % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    let minutes = Math.floor(
      (t % (1000 * 60 * 60)) / (1000 * 60));
    let seconds = Math.floor(
      (t % (1000 * 60)) / 1000);

    // Output the remaining time
    document.getElementById("demo").innerHTML =
      days + "D " + hours + "H " +
      minutes + "M " + seconds + "s ";

    // Output for over time
    if (t < 0) {
      clearInterval(x);
      document.getElementById("demo")
        .innerHTML = "EXPIRED";
    }
  }, 1000);

  /**
   * Animation on scroll
   */
  window.addEventListener('load', () => {
    AOS.init({
      duration: 1000,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    })
  });
})()
