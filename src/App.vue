<template>
  <router-view />
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');

:root {
  --text: #2F2F2F;
  --text-label: #565656;

  --global-bg: #EEEEEE;
  --popup-bg: white;
  --hover-bg: #DFDFDF;
  --scroll-bar: #666666;

  --btn: #389FD8;
  --btn-hover: #61BAEA;
}

::-webkit-scrollbar {
  width: 14px;
}

::-webkit-scrollbar-track {
  box-shadow: inset 0 0 14px 14px transparent;
  border: solid 4px transparent;
}

::-webkit-scrollbar-thumb {
  box-shadow: inset 0 0 14px 14px var(--scroll-bar);
  border: solid 4px transparent;
  border-radius: 14px;
}

::-webkit-scrollbar-button {
  display: none;
}

* {
  margin: 0;
  padding: 0;
  font: inherit;
  color: var(--text);
  box-sizing: border-box;
}

body {
  margin: 0;
  padding: 0;
  background-color: var(--global-bg);
  font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;
  user-select: none;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  scroll-behavior: smooth;
  overflow: hidden;
}

main {
  height: calc(100vh - 56px);
  overflow: auto;
}

main > div {
  width: 100%;
  max-width: 1200px;
  margin: 20px auto;
  padding: 0 12px;
}
</style>
