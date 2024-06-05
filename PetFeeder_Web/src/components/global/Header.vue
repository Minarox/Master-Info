<template>
  <header id="header">
    <article>
      <section>
        <router-link id="logo" :to="'/'">
          <img src="@/assets/img/logo_cerealis.png" alt="Logo Cerealis" />
          <p>Pet Feeder</p>
        </router-link>
      </section>
    </article>
  </header>
</template>

<script>
export default {
  name: "Header",
};
</script>

<style scoped>
#header {
  z-index: 99;
  position: sticky;
  top: 0;
  height: 64px;
  padding: 0 20px;
  display: flex;
  align-items: center;
  background-color: var(--bg-content);
  box-shadow: rgba(0, 0, 0, 0) 0 0 0 0, rgba(0, 0, 0, 0) 0 0 0 0,
    rgba(0, 0, 0, 0.1) 0 10px 15px -3px, rgba(0, 0, 0, 0.05) 0 4px 6px -2px;
}

#header article {
  width: 100%;
  max-width: 1500px;
  margin: 0 auto;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: space-between;
}

#header #logo {
  padding: 8px;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  gap: 16px;
  text-decoration: none;
  font-size: 1.5em;
  font-weight: 500;
}

#header #logo,
#header #logo img {
  height: 40px;
}

@media only screen and (max-width: 350px) {
  #logo p {
    display: none;
  }
}
</style>
