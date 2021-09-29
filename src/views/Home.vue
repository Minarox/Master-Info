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
          composant
        </div>
      </div>
    </section>
  </article>
  <footerbar />
</template>

<script>
import Footerbar from "@/components/footerbar";
export default {
  name: 'Home',
  components: {
    Footerbar
  },
}
</script>

<style scoped>
article {
  background-image: url("../assets/img/background.svg");
  background-repeat: no-repeat;
  background-position: top;
  margin: 0;
}

section {
  margin: 0 auto;
  max-width: 1200px;
}

section:first-of-type {
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
  font-family: "Montserrat", Roboto, Arial, sans-serif;
}

#devices {
  padding: 0 12px;
  width: 400px;
  height: auto;
}

section:last-of-type {
  margin: 20px auto 40px;
  padding: 20px;
  background-color: white;
  border-radius: 16px;
  box-shadow: rgba(0, 0, 0, 0.07) 0 1px 1px, rgba(0, 0, 0, 0.07) 0 2px 2px, rgba(0, 0, 0, 0.07) 0 4px 4px, rgba(0, 0, 0, 0.07) 0 8px 8px, rgba(0, 0, 0, 0.07) 0 16px 16px;
}

section:last-of-type h2 {
  text-align: center;
  font-size: 1.2em;
  font-weight: 600;
  margin: 0;
}

section:last-of-type p {
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
  font-family: "Montserrat", Roboto, Arial, sans-serif;
  font-weight: 600;
  font-size: 1.2em;
  margin-top: 8px;
}

#target {
  display: flex;
  flex-flow: row wrap;
  align-items: center;
  gap: 20px 0;
}

#target p {
  text-align: left;
}

#target div:first-of-type {
  max-width: 800px;
}

@media screen and (max-width: 750px) {
  #devices {
    display: none;
  }

  #survey a {
    flex: 1 0 50%;
  }
}

@media screen and (max-width: 500px) {
  h1 {
    font-size: 2em;
  }
}
</style>
