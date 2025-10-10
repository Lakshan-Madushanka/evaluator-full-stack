import { isDarkMode } from '@/helpers'

function createElm(el, targetElmId) {
  let container = document.createElement('div')
  let icon = document.createElement('i')

  el.style.position = 'relative'

  icon.className = 'pi pi-copy hover:cursor-pointer'

  icon.style.padding = '0'
  icon.style.backgroundColor = isDarkMode() ? 'black' : 'white'
  icon.style.fontSize = '16px'

  container.style.position = 'absolute'
  container.style.right = '-1.8rem'
  container.style.top = '-3px'

  container.appendChild(icon)

  icon.addEventListener('click', () => copyToClipBoard(container, targetElmId))

  el.appendChild(container)
}

function createToast(element) {
  let span = document.createElement('span')

  span.style.position = 'absolute'
  span.style.fontWeight = 'bold'
  span.style.left = '2rem'
  span.style.bottom = '1px'
  span.style.padding = '2px'
  span.style.backgroundColor = isDarkMode() ? 'black' : 'white'

  span.textContent = 'copied'

  element.appendChild(span)

  setTimeout(() => span.remove(), 1000)
}

function copyToClipBoard(element, targetElmId) {
  const target = getTarget(targetElmId)

  let content = target.value

  if (target.value === undefined) {
    content = target.textContent
  }

  navigator.clipboard.writeText(content)

  createToast(element)
}

function getTarget(targetElmId) {
  const target = document.querySelector('#' + targetElmId)

  return target
}

export default {
  beforeMount: (el, binding) => {
    createElm(el, binding.value)
  }
}
