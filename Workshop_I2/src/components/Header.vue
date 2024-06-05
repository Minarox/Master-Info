<template>
  <header>
    <router-link to="/">
      <img src="@/assets/img/logo.png" alt="Logo" />
      <h1>Localelec</h1>
    </router-link>
    <section>
      <router-link to="/#pourquoi-localelec">Pourquoi ce service ?</router-link>
      <router-link to="/#gestes-responsables">
        Les gestes responsables
        <font-awesome-icon icon="fa-solid fa-leaf" />
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
  padding: 0.6rem 1.5rem;
  background-color: var(--secondary-bg-color);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
  height: 80px;

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
      max-width: 46px;
      height: auto;
      max-height: 46px;
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

    > a:nth-of-type(2) svg {
      margin-left: 0.6rem;
      color: #02f0c6;
    }
  }
}
</style>
