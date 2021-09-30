<template>
  <article>
    <section>
      <h1> Actualités </h1>
    </section>
    <section>
      <a href="#">
        <img src="@/assets/img/news/directeur_cybersecurite_emprisonne.png" alt="Directeur d'une société de cybersécurité emprisonné">
        <header>
          Le directeur d’une société de cybersécurité emprisonné pour haute trahison !
        </header>
      </a>
      <a href="#">
        <img src="@/assets/img/news/t-mobile.jpg" alt="Vol de données chez T-Mobile">
        <header>
          Vol de données chez T-MOBILE : 100 million de personne aurait été pirater !
        </header>
      </a>
      <a href="#">
        <img src="@/assets/img/news/netflix.jpg" alt="Netflix, plateforme la moins respectueuses des données">
        <header>
          NETFLIX la plateforme là moins respectueuse des données de ces utilisateurs !
        </header>
      </a>
      <a href="#">
        <img src="@/assets/img/news/rancon-logiciel-wanacry.jpg" alt="Protection contre les rançon logiciel">
        <header>
          Comment se protéger face au rançon logiciel ?
        </header>
      </a>
    </section>
  </article>
</template>

<script>
export default {
  name: "News"
}
</script>

<style scoped>
article {
  background-image: url("../assets/img/background.svg");
  background-position: center top;
  background-size: 100.1% auto;
  background-repeat: no-repeat;
  background-position-y: -340px;
}

section {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 12px;
}

section:nth-of-type(1) {
  padding: 30px 0;
}

section:nth-of-type(1) h1 {
  margin: 0;
  font-size: 2.2em;
  color: var(--title);
  text-align: center;}

section:nth-of-type(2) {
  margin: 0 auto 20px;
  padding: 26px;
  background-color: white;
  border-radius: 16px;
  box-shadow: rgba(0, 0, 0, 0.07) 0 1px 1px, rgba(0, 0, 0, 0.07) 0 2px 2px, rgba(0, 0, 0, 0.07) 0 4px 4px, rgba(0, 0, 0, 0.07) 0 8px 8px, rgba(0, 0, 0, 0.07) 0 16px 16px;
  display: flex;
  flex-flow: row wrap;
  gap: 30px;
}

section:nth-of-type(2) a {
  flex: 0 0 calc(100% / 3 - 20px);
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  gap: 12px;
  color: var(--text);
  text-decoration: none;
  text-align: center;
  transform: scale(1);
  transition: transform .15s;
}

section:nth-of-type(2) a:hover {
  transform: scale(1.04);
  transition: transform .15s;
}

section:nth-of-type(2) img {
  aspect-ratio: 16/9;
  width: 280px;
  height: auto;
}

section:nth-of-type(2) header {
  max-width: 320px;
}

@media screen and (max-width: 1919px) {
  article {
    background-size: auto;
  }
}

@media screen and (max-width: 1000px) {
  section:nth-of-type(2) a {
    flex: 1 0 calc(100% / 2 - 20px);
  }
}
</style>