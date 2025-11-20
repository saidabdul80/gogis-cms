import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Public\NewsController::index
* @see app/Http/Controllers/Public/NewsController.php:11
* @route '/news'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/news',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Public\NewsController::index
* @see app/Http/Controllers/Public/NewsController.php:11
* @route '/news'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Public\NewsController::index
* @see app/Http/Controllers/Public/NewsController.php:11
* @route '/news'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\NewsController::index
* @see app/Http/Controllers/Public/NewsController.php:11
* @route '/news'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Public\NewsController::index
* @see app/Http/Controllers/Public/NewsController.php:11
* @route '/news'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\NewsController::index
* @see app/Http/Controllers/Public/NewsController.php:11
* @route '/news'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\NewsController::index
* @see app/Http/Controllers/Public/NewsController.php:11
* @route '/news'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Public\NewsController::show
* @see app/Http/Controllers/Public/NewsController.php:20
* @route '/news/{news}'
*/
export const show = (args: { news: string | { slug: string } } | [news: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/news/{news}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Public\NewsController::show
* @see app/Http/Controllers/Public/NewsController.php:20
* @route '/news/{news}'
*/
show.url = (args: { news: string | { slug: string } } | [news: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { news: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { news: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            news: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        news: typeof args.news === 'object'
        ? args.news.slug
        : args.news,
    }

    return show.definition.url
            .replace('{news}', parsedArgs.news.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Public\NewsController::show
* @see app/Http/Controllers/Public/NewsController.php:20
* @route '/news/{news}'
*/
show.get = (args: { news: string | { slug: string } } | [news: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\NewsController::show
* @see app/Http/Controllers/Public/NewsController.php:20
* @route '/news/{news}'
*/
show.head = (args: { news: string | { slug: string } } | [news: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Public\NewsController::show
* @see app/Http/Controllers/Public/NewsController.php:20
* @route '/news/{news}'
*/
const showForm = (args: { news: string | { slug: string } } | [news: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\NewsController::show
* @see app/Http/Controllers/Public/NewsController.php:20
* @route '/news/{news}'
*/
showForm.get = (args: { news: string | { slug: string } } | [news: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\NewsController::show
* @see app/Http/Controllers/Public/NewsController.php:20
* @route '/news/{news}'
*/
showForm.head = (args: { news: string | { slug: string } } | [news: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const NewsController = { index, show }

export default NewsController