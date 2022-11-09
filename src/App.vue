<template>
  <Header />

  <router-view />

  <Footer />
</template>

<script>
import Header from "@/components/Header";
import Footer from "@/components/Footer";

export default {
  components: {
    Header,
    Footer,
  },
};
</script>

<style lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap");

:root {
  --font-family: "Montserrat", "Georgia", "Arial", sans-serif;
  --max-width: 1300px;
  --border-radius: 2px;
  --primary-bg-color: #090817;
  --secondary-bg-color: #1e1c3b;
  --green-touch: #02f0c6;
  --primary-text-color: #ffffff;
  --secondary-text-color: #edebe8;
}

::-webkit-scrollbar {
  width: 14px;
}

::-webkit-scrollbar-thumb {
  box-shadow: inset 0 0 14px 14px var(--primary-text-color);
  border: solid 4px transparent;
  border-radius: 14px;
}

::-webkit-scrollbar-button {
  display: none;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  user-select: none;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  scroll-behavior: smooth;
  background-color: var(--primary-bg-color);
  color: var(--primary-text-color);
  font-family: var(--font-family);
  overflow: overlay;

  img {
    -webkit-user-drag: none;
    -khtml-user-drag: none;
    -moz-user-drag: none;
    -o-user-drag: none;
    user-drag: none;
  }
}

.content {
  width: min(100% - 2rem, var(--max-width));
  margin-inline: auto;
}
</style>
