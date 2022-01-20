<template>
  <router-view />
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

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

form label {
  display: block;
  margin: 0 0 6px 4px;
  color: var(--text-label);
}

form input, form select {
  display: block;
  width: 100%;
  outline: none;
  padding: 6px;
  height: 34px;
  border: none;
  border-bottom: 1px solid var(--text-label);
}

form button {
  display: block;
  margin: 0 auto;
  outline: none;
  cursor: pointer;
  border-radius: 3px;
  font-size: 14px;
  font-weight: 500;
  line-height: 16px;
  padding: 10px 24px;
  border: none;
  color: #fff;
  background-color: var(--btn);
  transition: background-color .17s ease,color .17s ease;
}

form button:hover {
  background-color: var(--btn-hover);
}

form button > * {
  color: #fff;
}
</style>
