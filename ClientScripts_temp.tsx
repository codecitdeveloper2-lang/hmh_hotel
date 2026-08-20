"use client";
import { useEffect } from "react";
import { usePathname } from "next/navigation";

declare global {
  interface Window {
    $: any;
  }
}

export default function ClientScripts() {
  const pathname = usePathname();

  useEffect(() => {
    if (typeof window === "undefined" || !window.$) return;
    const $ = window.$;

    // Give a slight delay to allow React to paint DOM elements before jQuery manipulates them
    const timer = setTimeout(() => {
      // Submenu toggle for mobile
      document.querySelectorAll('.dropdown-submenu > a').forEach(function (element) {
          // Remove old listeners to prevent duplicates on remount
          const newEl = element.cloneNode(true) as HTMLElement;
          element.parentNode?.replaceChild(newEl, element);
          newEl.addEventListener('click', function (e) {
              if (window.innerWidth < 992) {
                  e.preventDefault();
                  e.stopPropagation();
                  let parent = this.parentElement;
                  if (!parent) return;
                  let siblingSubmenus = parent.parentElement?.querySelectorAll('.dropdown-submenu');
                  // Close other submenus at the same level
                  siblingSubmenus?.forEach(function (el) {
                      if (el !== parent) {
                          el.classList.remove('show');
                      }
                  });
                  // Toggle current submenu
                  parent.classList.toggle('show');
              }
          });
      });

      // Offers Carousel
      if ($('.offers-carousel').length && !$('.offers-carousel').hasClass('owl-loaded')) {
          var owl = $('.offers-carousel').owlCarousel({
              loop: true,
              margin: 30,
              nav: false,
              dots: false,
              autoplay: true,
              autoplayTimeout: 4000,
              autoplayHoverPause: true,
              responsive: {
                  0: { items: 1, margin: 15 },
                  768: { items: 2, margin: 20 },
                  992: { items: 3, margin: 30 }
              }
          });
          $('.offers-carousel-nav .next-btn').off('click').on('click', function () {
              owl.trigger('next.owl.carousel');
          });
          $('.offers-carousel-nav .prev-btn').off('click').on('click', function () {
              owl.trigger('prev.owl.carousel');
          });
      }

      // Accommodation Carousel
      if ($('.accommodation-carousel').length && !$('.accommodation-carousel').hasClass('owl-loaded')) {
          var accommodationOwl = $('.accommodation-carousel').owlCarousel({
              loop: true,
              items: 1,
              margin: 0,
              nav: false,
              dots: false,
              autoplay: true,
              autoplayTimeout: 5000,
              autoplayHoverPause: true
          });
          $('.accommodation-section .next-btn').off('click').on('click', function () {
              accommodationOwl.trigger('next.owl.carousel');
          });
          $('.accommodation-section .prev-btn').off('click').on('click', function () {
              accommodationOwl.trigger('prev.owl.carousel');
          });
          $('.accommodation-list-item').off('click').on('click', function (this: HTMLElement) {
              var slideIndex = parseInt($(this).attr('data-slide') || '0');
              accommodationOwl.trigger('to.owl.carousel', [slideIndex, 400]);
          });
          accommodationOwl.on('changed.owl.carousel', function (event: any) {
              if (event.item) {
                  // This is a rough logic translated from main.js
                  var realIndex = event.item.index - Math.floor(event.item.count / 2);
                  if (realIndex < 0) realIndex = event.item.count + realIndex;
                  // The original had: event.relatedTarget.relative(event.item.index)
                  // For safety, we can just use the provided method if it exists
                  if (event.relatedTarget && event.relatedTarget.relative) {
                      realIndex = event.relatedTarget.relative(event.item.index);
                  }
                  $('.accommodation-list-item').removeClass('active');
                  $('.accommodation-list-item[data-slide="' + realIndex + '"]').addClass('active');
              }
          });
      }

      // Amenities Carousel
      if ($('.amenities-carousel').length && !$('.amenities-carousel').hasClass('owl-loaded')) {
          var amenitiesOwl = $('.amenities-carousel').owlCarousel({
              loop: true,
              items: 1,
              margin: 0,
              nav: false,
              dots: false,
              autoplay: true,
              autoplayTimeout: 5000,
              animateOut: 'fadeOut',
              animateIn: 'fadeIn'
          });
          $('.amenities-section .next-btn').off('click').on('click', function () {
              amenitiesOwl.trigger('next.owl.carousel');
          });
          $('.amenities-section .prev-btn').off('click').on('click', function () {
              amenitiesOwl.trigger('prev.owl.carousel');
          });
      }

      // Explore Carousel
      if ($('.explore-carousel').length && !$('.explore-carousel').hasClass('owl-loaded')) {
          var exploreOwl = $('.explore-carousel').owlCarousel({
              loop: true,
              margin: 30,
              nav: false,
              dots: false,
              autoplay: true,
              autoplayTimeout: 4000,
              autoplayHoverPause: true,
              responsive: {
                  0: { items: 1, margin: 15 },
                  768: { items: 2, margin: 20 },
                  992: { items: 4, margin: 30 }
              }
          });
          $('.explore-carousel-nav .next-btn').off('click').on('click', function () {
              exploreOwl.trigger('next.owl.carousel');
          });
          $('.explore-carousel-nav .prev-btn').off('click').on('click', function () {
              exploreOwl.trigger('prev.owl.carousel');
          });
      }

    }, 100);

    return () => clearTimeout(timer);
  }, [pathname]);

  return null;
}
