import { helpers } from '@vuelidate/validators'

export const password = helpers.regex(
  /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/
)

export const exists = (allowedValues) => (value) => {
  return value === '' || allowedValues.includes(value)
}

export const messages = {
  password:
    'Password should contains, ' +
    'at least one lowercase, ' +
    'one uppercase, ' +
    'one numeric, ' +
    'one special character and minimum 8 characters.',
  exists: 'Invalid Value'
}
