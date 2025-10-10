const apiURL = import.meta.env.VITE_API_URL

export const get_route_to_upload_images = (id, type) => `${apiURL}uploads/${type}/${id}`
export const get_route_to_load_images = (id, type) => `${apiURL}uploads/${type}/${id}`
export const get_route_to_change_order_of_images = (type) => `uploads-change-order/${type}`
export const get_route_to_remove_images = (type) => `uploads-mass-delete/${type}`
