export const get_all_route = 'administrative/users'
export const get_one_route = (id) => `administrative/users/${id}`
export const get_create_route = 'super-admin/users'
export const get_update_route = (id) => `super-admin/users/${id}`
export const get_delete_route = (userId) => `super-admin/users/${userId}`
export const get_bulk_delete_route = () => `super-admin/users/mass-delete`
