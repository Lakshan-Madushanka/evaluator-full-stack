export function buildQueryString(payload) {
  let queryString

  if (!payload || !payload.query) {
    return ''
  }
  queryString = payload.query

  let query = '?'

  if (queryString.pagination) {
    query += buildPagination(queryString.pagination) + '&'
  }

  if (queryString.sort) {
    query += buildSorts(queryString.sort) + '&'
  }

  if (queryString.filters && Object.keys(queryString.filters).length !== 0) {
    query += buildFilters(queryString.filters) + '&'
  }

  if (queryString.includes && queryString.includes.length > 0) {
    query += includeRelationships(queryString.includes) + '&'
  }

  return query
}

function buildPagination(data) {
  const params = new URLSearchParams({
    'page[number]': data.number,
    'page[size]': data.size
  })

  return params.toString()
}

function buildSorts(sorts) {
  const params = new URLSearchParams()
  let tmpSorts = []

  for (let sort in sorts) {
    if (sorts[sort] === 'asc') {
      tmpSorts.push(sort)
    }

    if (sorts[sort] === 'desc') {
      tmpSorts.push(`-${sort}`)
    }
  }

  if (tmpSorts.length === 0) {
    return ''
  }

  params.set('sort', tmpSorts.join(','))

  return params.toString()
}

function buildFilters(filters) {
  const params = new URLSearchParams()

  for (const filter in filters) {
    if (filters[filter] === null || filters[filter] === '') {
      continue
    }
    let filterValue = filters[filter]

    if (Array.isArray(filterValue)) {
      filterValue = filterValue.join(',')
    } else if (typeof filterValue === 'object') {
      if (filterValue['value'] === null) {
        continue
      }
      filterValue = filterValue.value
    }

    params.set(`filter[${filter}]`, filterValue)
  }

  return params.toString()
}

function includeRelationships(relationships) {
  const params = new URLSearchParams({ include: relationships.join(',') })
  return params.toString()
}
