<template>
  <main>
    <article>
      <div>
        <section class="content">
          <div>
            <h2>Localelec</h2>
            <p>Des données précises pour des économies précises</p>
          </div>
          <SelectCity />
        </section>
        <img
          id="separator_1"
          src="@/assets/svg/separator_1.svg"
          alt="Separator"
        />
      </div>

      <div>
        <section class="content">
          <h3 id="pourquoi-localelec">Pourquoi Localelec ?</h3>
          <p>
            Localelec vous permet d'en apprendre plus sur la consommation
            électrique de votre commune et les tendances qui en découlent.<br />
            Cela vous permet de mieux consommer l'électricité en agissant à
            toutes les échelles et en prenant des mesures utiles et efficaces.
          </p>
        </section>
        <section class="content">
          <h3>Les analyses de Localelec</h3>
          <div>
            <Splide
              class="splide"
              :has-track="false"
              :options="options"
              aria-label="analyses"
            >
              <SplideTrack>
                <SplideSlide>
                  <img src="@/assets/svg/fake_graph.svg" alt="Graph" />
                  <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Phasellus orci magna, tincidunt vitae magna eu, lacinia
                    dictum metus. Ut pretium euismod ligula, eget blandit arcu
                    finibus sodales. Nam sem magna, tristique quis augue ut,
                    fermentum elementum leo. Etiam nec justo non quam ultricies
                    scelerisque. Fusce sit amet felis nunc. Curabitur finibus
                    magna vitae sodales viverra.
                  </p>
                </SplideSlide>
                <SplideSlide>
                  <img src="@/assets/svg/fake_graph.svg" alt="Graph" />
                  <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Phasellus orci magna, tincidunt vitae magna eu, lacinia
                    dictum metus. Ut pretium euismod ligula, eget blandit arcu
                    finibus sodales. Nam sem magna, tristique quis augue ut,
                    fermentum elementum leo. Etiam nec justo non quam ultricies
                    scelerisque. Fusce sit amet felis nunc. Curabitur finibus
                    magna vitae sodales viverra.
                  </p>
                </SplideSlide>
              </SplideTrack>

              <div class="splide__arrows">
                <button class="splide__arrow splide__arrow--prev">
                  <font-awesome-icon icon="fa-solid fa-angle-right" />
                </button>
                <button class="splide__arrow splide__arrow--next">
                  <font-awesome-icon icon="fa-solid fa-angle-right" />
                </button>
              </div>

              <ul class="splide__pagination"></ul>
            </Splide>
          </div>
        </section>

        <img
          id="separator_2"
          src="@/assets/svg/separator_2.svg"
          alt="Separtor"
        />
      </div>
    </article>

    <article class="content">
      <h3 id="gestes-responsables">Les gestes responsables</h3>
      <div>
        <section>
          <img
            src="@/assets/svg/ecogeste-chauffage.svg"
            alt="Eco-geste chauffage"
          />
          <p>Réduire autant que possible la température du chauffage</p>
        </section>
        <section>
          <img
            src="@/assets/svg/ecogeste-electromenager.svg"
            alt="Eco-geste electromenager"
          />
          <p>
            Décaler certains usages domestiques (lave-vaisselle, machine à
            laver, etc.)
          </p>
        </section>
        <section>
          <img
            src="@/assets/svg/ecogeste-cuisine.svg"
            alt="Eco-geste cuisine"
          />
          <p>
            Modérer l'utilisation des appareils de cuisson (par exemple en
            évitant les cuissons longues)
          </p>
        </section>
        <section>
          <img
            src="@/assets/svg/ecogeste-lumiere.svg"
            alt="Eco-geste lumiere"
          />
          <p>Éteindre les lumières inutiles</p>
        </section>
        <section>
          <img
            src="@/assets/svg/ecogeste-chauffage.svg"
            alt="Eco-geste chauffage"
          />
          <p>
            Prévoir une programmation spécifique du chauffage (par ex. en
            réduisant la température de 1°C supplémentaire)
          </p>
        </section>
        <section>
          <img src="@/assets/svg/ventilateur.svg" alt="Eco-geste ventilateur" />
          <p>
            Dans les locaux tertiaires, réduire le débit de ventilation au
            strict besoin du bâtiment
          </p>
        </section>
        <section>
          <img
            src="@/assets/svg/ecogeste-lumiere.svg"
            alt="Eco-geste lumiere"
          />
          <p>Réduire l'éclairage intérieur des bâtiments</p>
        </section>
        <section>
          <img
            src="@/assets/svg/ecogeste-lumiere-2.svg"
            alt="Eco-geste éclairage public"
          />
          <p>Réduire l'éclairage public</p>
        </section>
        <section>
          <img
            src="@/assets/svg/ecogeste-voiture.svg"
            alt="Eco-geste voitures"
          />
          <p>
            Éviter de recharger les véhicules électriques pendant les périodes
            de tension
          </p>
        </section>
        <section>
          <img
            src="@/assets/svg/ecogeste-publicite.svg"
            alt="Eco-geste publicités"
          />
          <p>
            Couper les affichages et éclairages non essentiels (par ex. : les
            panneaux publicitaires)
          </p>
        </section>
        <section>
          <img
            src="@/assets/svg/ecogeste-robinet.svg"
            alt="Eco-geste robinets"
          />
          <p>Réduire l'utilisation de l'eau chaude</p>
        </section>
      </div>
    </article>
  </main>
</template>

<script>
import SelectCity from "@/components/SelectCity";
import { Splide, SplideTrack, SplideSlide } from "@splidejs/vue-splide";
import "@splidejs/vue-splide/css";

export default {
  name: "HomeView",
  components: {
    SelectCity,
    Splide,
    SplideTrack,
    SplideSlide,
  },
  data() {
    return {
      options: {
        type: "loop",
        rewind: true,
        rewindByDrag: true,
        autoplay: true,
        keyboard: true,
        heightRatio: 0.4,
        speed: 350,
        rewindSpeed: this.speed,
        easing: "cubic-bezier(0.25, 0, 0.25, 1)",
        interval: 7500,
      },
    };
  },
};
</script>

<style scoped lang="scss">
article:nth-of-type(1) {
  background-color: var(--secondary-bg-color);

  > div:first-of-type {
    position: relative;
    height: 600px;
    background-image: url("@/assets/img/background.png");
    background-color: var(--primary-bg-color);

    > section {
      z-index: 1;
      position: absolute;
      top: 25%;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      flex-flow: row nowrap;
      align-items: center;
      justify-content: space-between;
      gap: 4rem;

      div {
        display: flex;
        flex-flow: column nowrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1.8rem;
        text-shadow: 0 10px 20px rgba(0, 0, 0, 0.1),
          0 6px 6px rgba(0, 0, 0, 0.1);

        h2 {
          font-size: 4.4em;
        }

        p {
          font-size: 2.7em;
          font-weight: bold;
        }
      }
    }
  }

  #separator_1 {
    display: block;
    position: absolute;
    bottom: -35%;
  }

  > div:nth-of-type(2) {
    z-index: 0;
    position: relative;
    margin-top: 7rem;
    text-align: center;
    display: flex;
    flex-flow: column nowrap;
    align-items: center;
    justify-content: space-between;
    gap: 7rem;

    section {
      display: flex;
      flex-flow: column nowrap;
      align-items: center;
      justify-content: space-between;
      gap: 2rem;

      h3 {
        font-size: 1.9em;
      }

      p {
        font-size: 1.1em;
        line-height: 1.5rem;
      }

      div {
        width: 100%;
        height: 360px;
        border-radius: 16px;
        background-color: var(--primary-bg-color);

        .splide {
          border-radius: 16px;
          overflow: hidden;
          box-shadow: rgba(0, 0, 0, 0.15) 0 8px 16px 0;
          position: relative;

          .splide__slide {
            padding: 5rem;
            height: 100% !important;
            display: flex;
            flex-flow: row nowrap;
            align-items: center;
            justify-content: space-between;
            gap: 5rem;

            img {
              width: 530px;
              height: 300px;
            }
          }

          .splide__arrow {
            height: 100%;
            border-radius: unset;
            color: var(--text-color);
            font-size: 1.2em;
            padding: 0 1rem;
            width: auto;
            transition: all 250ms;
          }

          .splide__arrow--prev {
            left: 0;
            background-color: rgba(0, 0, 0, 0);
          }

          .splide__arrow--next {
            right: 0;
            background-color: rgba(0, 0, 0, 0);
          }

          .splide__pagination {
            gap: 0.5rem;
            bottom: 0.7rem;
          }
        }
      }
    }
  }

  #separator_2 {
    z-index: -1;
    position: absolute;
    bottom: -40%;
  }
}

article:nth-of-type(2) {
  margin-top: 20rem;
  margin-bottom: 6rem;
  text-align: center;

  h3 {
    font-size: 1.9em;
  }

  > div {
    margin-top: 2rem;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;

    section {
      background-color: var(--secondary-bg-color);
      border-radius: 16px;
      padding: 2rem;
      display: flex;
      flex-flow: column nowrap;
      align-items: center;
      justify-content: flex-start;
      gap: 2rem;

      img {
        width: 120px;
        height: 120px;
        border-radius: 100%;
        border: 4px solid var(--green-touch);
        padding: 0.4rem;
      }

      p {
        font-size: 1.1rem;
      }
    }
  }
}
</style>
