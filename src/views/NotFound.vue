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
  margin: 20px auto 0;
  padding: 12px;
}

h1 {
  text-align: center;
  font-size: 1.6em;
}

section {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
}

a {
  padding: 12px 20px;
  text-align: center;
  font-size: 1.1em;
  border-radius: 8px;
  background-color: #DFDFDF;
  color: var(--primary-text);
  text-decoration: none;
}
</style>