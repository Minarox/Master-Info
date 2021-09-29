<template>
  <article>
    <h1>Page introuvable</h1>
    <section>
      <a href="/">Retour à l'accueil</a>
    </section>
  </article>
</template>

<script>
export default {
  name: "NotFound"
}
</script>

<style scoped>
article {
  background-image: url("../assets/img/background.svg");
  background-position: center top;
  background-size: 100.1% auto;
  background-repeat: no-repeat;
  background-position-y: -360px;
}

h1 {
  margin: 0;
  padding: 30px 12px;
  text-align: center;
  font-size: 1.8em;
  color: var(--title);
  font-family: "Montserrat", Roboto, Arial, sans-serif;
}

section {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
}

a {
  font-size: 1em;
  text-align: center;
  padding: 12px 100px;
  margin-bottom: 40px;
  border-radius: 8px;
  color: var(--text) !important;
  text-decoration: none;
  background-color: white;
  border: 1px solid black;
  box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;
  transform: scale(1);
  transition: background-color .15s, transform .15s;
}

a:hover {
  background-color: #DDDDDD;
  transform: scale(1.04);
  transition: background-color .15s, transform .15s;
}

@media screen and (max-width: 1919px) {
  article {
    background-size: auto;
  }
}
</style>