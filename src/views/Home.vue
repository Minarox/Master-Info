<template>
  <article>
    <section>
      <h1>
        Testez vos connaissances <br>
        en matière de <br>
        sécurité informatique
      </h1>
      <img id="devices" src="@/assets/img/devices_security.png" alt="Sécurité des appareils">
    </section>

    <section>
      <h2>Connaissez-vous vraiment les dangers auxquels vous êtes exposés chaque jour sur internet ?</h2>
      <p>
        Grâce à nos différents quiz que nous vous proposons, vous pourrez tester vos connaissances sur bon nombre de domaines
        et ainsi améliorer vos capacités à vous protéger en approfondissant vos connaissances.
      </p>
      <div id="survey">
        <a href="/survey/0/0">
          <img src="@/assets/img/password.png" alt="Mot de passe">
          <p>Mot de passe</p>
        </a>
        <a href="/survey/1/0">
          <img src="@/assets/img/phishing.png" alt="Phishing">
          <p>Phishing</p>
        </a>
        <a href="/survey/2/0">
          <img src="@/assets/img/devices.png" alt="Matériel informatique">
          <p>Matériel informatique</p>
        </a>
      </div>
      <hr>
      <div id="target">
        <div>
          <h3>Vous êtes un particulier ? Un professionnel ?</h3>
          <p>Découvrez les quiz sur des domaines qui vous permettrons de mieux reconnaitre et déjouer les attaques auxquelles vous pouvez faire face durant votre navigation sur internet.</p>
        </div>
        <div>
          <span>
            <a href="#" @click.prevent="survey_list = 'particulier'">
              <span v-if="survey_list === 'particulier'">
                <strong>Particulier</strong>
              </span>
              <span v-else>
                Particulier
              </span>
            </a>
            &nbsp;|&nbsp; <a href="#" @click.prevent="survey_list = 'professionnel'">
              <span v-if="survey_list === 'professionnel'">
                <strong>Professionnel</strong>
              </span>
              <span v-else>
                Professionnel
              </span>
            </a>
          </span>
          <Survey_list :list="survey_list" />
        </div>
      </div>
    </section>

    <section>
      <h3>Dernières actualités :</h3>
      <div id="news">
        <a href="#">
          <img src="@/assets/img/news/directeur_cybersecurite_emprisonne.png" alt="Directeur d'une société de cybersécurité emprisonné">
          <header>
            Le directeur d’une société de cybersécurité emprisonné pour haute trahison !
          </header>
        </a>
        <hr>
        <a href="#">
          <img src="@/assets/img/news/t-mobile.jpg" alt="Vol de données chez T-Mobile">
          <header>
            Vol de données chez T-MOBILE : 100 million de personne aurait été pirater !
          </header>
        </a>
        <hr>
        <a href="#">
          <img src="@/assets/img/news/netflix.jpg" alt="Netflix, plateforme la moins respectueuses des données">
          <header>
            NETFLIX la plateforme là moins respectueuse des données de ces utilisateurs !
          </header>
        </a>
      </div>
      <a href="/news" id="more_news">Plus d'actualités</a>
    </section>
  </article>
  <footerbar />
</template>

<script>
import Footerbar from "@/components/footerbar";
import Survey_list from "@/components/survey_list";

export default {
  name: 'Home',
  components: {
    Survey_list,
    Footerbar
  },
  data() {
    return {
      survey_list: 'particulier'
    }
  }
}
</script>

<style scoped>
article {
  background-image: url("../assets/img/background.svg");
  background-repeat: no-repeat;
  background-position: center top;
  background-size: 100.1% auto;
}

section {
  margin: 0 auto;
  max-width: 1200px;
  padding: 0 12px;
}

section:nth-of-type(1) {
  padding: 40px 0;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: space-evenly;
}

h1 {
  margin: 0;
  padding: 0 12px;
  color: var(--title);
  text-align: center;
  font-size: 2.4em;
}

#devices {
  padding: 0 12px;
  width: 400px;
  height: auto;
}

section:nth-of-type(2) {
  margin: 20px auto 40px;
  padding: 20px;
  background-color: white;
  border-radius: 16px;
  box-shadow: rgba(0, 0, 0, 0.07) 0 1px 1px, rgba(0, 0, 0, 0.07) 0 2px 2px, rgba(0, 0, 0, 0.07) 0 4px 4px, rgba(0, 0, 0, 0.07) 0 8px 8px, rgba(0, 0, 0, 0.07) 0 16px 16px;
}

section:nth-of-type(2) h2 {
  text-align: center;
  font-size: 1.2em;
  font-weight: 600;
  margin: 0;
}

section:nth-of-type(2) p {
  text-align: center;
  margin: 4px 0;
  font-size: 1.1em;
  font-weight: 300;
}

#survey {
  margin: 20px 0;
  display: flex;
  flex-flow: row wrap;
  align-items: center;
  justify-content: space-around;
  gap: 20px 0;
}

#survey img {
  width: auto;
  height: 100px;
}

#survey a {
  text-decoration: none;
  color: var(--text);
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  transform: scale(1);
  transition: transform .15s;
}

#survey a:hover {
  transform: scale(1.06);
  transition: transform .15s;
}

#survey p {
  font-weight: 600;
  font-size: 1.2em;
  margin-top: 8px;
}

section:nth-of-type(2) hr {
  margin: 16px 0;
}

#target {
  display: flex;
  flex-flow: row wrap;
  align-items: center;
  justify-content: space-between;
  gap: 20px 0;
}

#target p {
  text-align: left;
}

#target div:first-of-type {
  max-width: 700px;
}

#target div:last-of-type {
  display: flex;
  flex-flow: column wrap;
  align-items: center;
  gap: 12px 0;
}

#target div:last-of-type a {
  color: var(--text);
  text-decoration: none;
}

#news {
  display: flex;
  flex-flow: row nowrap;
  align-items: baseline;
  justify-content: space-evenly;
  gap: 28px 0;
}

#news a {
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  justify-content: center;
  gap: 12px 0;
  text-align: center;
  color: var(--text);
  text-decoration: none;
  transform: scale(1);
  transition: transform .15s;
}

#news a:hover {
  transform: scale(1.04);
  transition: transform .15s;
}

#news img {
  aspect-ratio: 16/9;
  width: 260px;
  height: auto;
}

#news header {
  max-width: 320px;
}

#news hr {
  margin: 0 20px;
  width: 1px;
  height: 130px;
  border: none;
  border-left: 2px solid var(--text);
}

#more_news {
  display: block;
  font-size: 1em;
  text-align: center;
  width: 340px;
  padding: 12px 0;
  margin: 30px auto 40px;
  border-radius: 8px;
  color: var(--text) !important;
  text-decoration: none;
  background-color: white;
  border: 1px solid black;
  box-shadow: rgba(50, 50, 93, 0.25) 0 6px 12px -2px, rgba(0, 0, 0, 0.3) 0 3px 7px -3px;
  transform: scale(1);
  transition: background-color .15s, transform .15s;
}

#more_news:hover {
  background-color: #DDDDDD;
  transform: scale(1.04);
  transition: background-color .15s, transform .15s;
}

@media screen and (max-width: 1919px) {
  article {
    background-size: auto;
  }
}

@media screen and (max-width: 950px) and (min-width: 550px) {
  #news a:nth-of-type(3), #news hr:nth-of-type(2) {
    display: none;
  }
}

@media screen and (max-width: 750px) {
  #devices {
    display: none;
  }

  #survey a {
    flex: 1 0 50%;
    min-width: 150px;
  }
}

@media screen and (max-width: 550px) {
  #news {
    flex-flow: column nowrap;
    align-items: center;
  }

  #news hr {
    display: none;
  }
}

@media screen and (max-width: 500px) {
  h1 {
    font-size: 2em;
  }

  #more_news {
    width: 100%;
  }
}
</style>
