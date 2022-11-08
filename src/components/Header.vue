<template>
  <header>
    <router-link to="/">
      <img src="@/assets/img/logo.png" alt="Logo" />
      <h1>Nom du site</h1>
    </router-link>
    <section>
      <router-link to="/">
        <font-awesome-icon icon="fa-solid fa-leaf" />
        Les gestes responsables
      </router-link>
      <router-link to="/">
        <font-awesome-icon icon="fa-solid fa-circle-question" />
        Pourquoi ce service ?
      </router-link>
      <a href="https://monecowatt.fr/">
        <img src="@/assets/img/ecowatt.webp" alt="Icon EcoWatt" />
        <font-awesome-icon icon="fa-solid fa-arrow-up-right-from-square" />
      </a>
    </section>
  </header>
</template>

<script>
export default {
  name: "HeaderComponent",
};
</script>

<style scoped lang="scss">
header {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: space-between;
  padding: 0.6rem 1rem;
  background-color: var(--secondary-bg-color);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);

  > a,
  > section {
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    gap: 1.6rem;

    &,
    a {
      text-decoration: none;
      color: var(--primary-text-color);
      font-size: 1em;
    }

    img {
      width: auto;
      max-width: 60px;
      height: auto;
      max-height: 60px;
    }

    > h1 {
      font-size: 1.5em;
    }

    > a {
      position: relative;
      padding: 0.6rem;
      display: flex;
      flex-flow: row nowrap;
      align-items: center;

      img {
        margin-right: 0.6rem;
      }

      &::after {
        content: "";
        position: absolute;
        top: 99%;
        bottom: 0;
        left: 0.6rem;
        right: 100%;
        background-color: var(--primary-text-color);
        transition: all 350ms;
      }

      &:hover::after {
        right: 0.6rem;
      }
    }

    > a:first-of-type svg {
      margin-right: 0.6rem;
      color: #02f0c6;
    }
  }
}
</style>
