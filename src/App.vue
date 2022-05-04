<template>
  <router-view />
</template>

<style>
@import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap");

:root {
  --title: var(--bg-color-semi-right);
  --text: #2f2f2f;
  --text-label: #565656;

  --bg-color-left: #2c5364;
  --bg-color-semi-left: #264755;
  --bg-color-center: #203a43;
  --bg-color-semi-right: #172C34;
  --bg-color-right: #0f2027;

  --bg-content: white;
  --bg-content-header: rgba(15, 32, 39, 0.12);
  --scroll-bar: var(--bg-color-semi-right);

  --btn: #2c5364;
  --btn-hover: #427185;

  --btn-warning: #bc4747;
  --btn-warning-hover: #e05353;

  --btn-back: #575757;
  --btn-back-hover: #797979;
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
  background: var(--bg-color-center);
  background: linear-gradient(to right, var(--bg-color-left), var(--bg-color-center), var(--bg-color-right));
  font-family: "Roboto", "Helvetica", "Arial", sans-serif;
  user-select: none;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  scroll-behavior: smooth;
  overflow: hidden;
}

main {
  height: calc(100vh - 64px);
  overflow: auto;
}

main > div {
  width: 100%;
  max-width: 1200px;
  margin: 30px auto;
  padding: 0 12px;
}

form label {
  display: block;
  margin: 0 0 6px 4px;
  color: var(--text-label);
}

form input,
form select {
  display: block;
  width: 100%;
  outline: none;
  padding: 8px;
  height: 36px;
  border: 1px solid var(--text-label);
  border-radius: 8px;
  margin-bottom: 26px;
}

form button,
.button {
  display: block;
  margin: 0 auto;
  outline: none;
  cursor: pointer;
  border-radius: 3px;
  font-weight: 500;
  line-height: 16px;
  padding: 10px 24px;
  border: none;
  color: white;
  background-color: var(--btn);
  transition: background-color 0.17s ease, color 0.17s ease;
  text-decoration: none;
  text-align: center;
}

.btn-warning {
  background-color: var(--btn-warning) !important;
}

.btn-back {
  background-color: var(--btn-back) !important;
}

form button:hover,
.button:hover {
  background-color: var(--btn-hover);
}

.btn-warning:hover {
  background-color: var(--btn-warning-hover) !important;
}

.btn-back:hover {
  background-color: var(--btn-back-hover) !important;
}

form button > * {
  color: white;
}

.popup {
  z-index: 1;
  padding: 12px;
  position: absolute;
  top: 64px;
  width: 100vw;
  height: calc(100vh - 64px);
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
}

.popup article {
  width: 100%;
  max-width: 600px;
  margin: 0 auto;
  border-radius: 12px;
  background-color: var(--bg-content);
  box-shadow: rgba(149, 157, 165, 0.2) 0 8px 24px 0;
}

.popup article > header {
  background-color: var(--bg-content-header);
  padding: 26px 12px;
  text-align: center;
  border-radius: 12px 12px 0 0;
  font-size: 1.5em;
  font-weight: 600;
}

.popup article > header > * {
  color: var(--title);
}

.popup article > section {
  padding: 20px;
  overflow: auto;
  max-height: calc(100vh - 140px);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>