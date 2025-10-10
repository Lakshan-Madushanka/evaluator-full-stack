export function sanitizeParam(param) {
  param = param.replace(/\s/g, '')
  param = param.replace(/\//g, '')
  param = param.replace(/\\/g, '')
  param = param.replace(/#/g, '')

  return param
}
