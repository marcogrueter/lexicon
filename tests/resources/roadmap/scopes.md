distant node
- type: block
- name: page.blogs
- parent source: $pageBlogs
- child source: $pageBlogsItem
- as: blog - alias of page.blogs

node
- type: block
- name: posts
- parent source: $pageBlogsItem
- child source: $postsItem
- as: post

child node
- type: variable
- name: post
- parent source $postsItem
- distant parent source: $blogs
- child source: none

{{ title }} ->variable($postItem, 'title')
{{ .post.title }} ->variable($postItem, 'title')
{{ .blog.title }} ->variable($blogItem, 'title')
{{ blog.title }} ->variable($postItem, 'blog.title') // Even if it returns null, it was intended

{{ libraries as library }}
    {{ categories as category }}
        {{ posts }}
            {{ title }} // not using . prefix defaults to current context
            {{ .category.title }}
            {{ .category.thing.title }}
            {{ .library.title }}
        {{ /posts }}
    {{ categories }}
{{ /libraries }}

If library alias is found
->variable($categoriesItem, 'title')
else
->variable($postItem, 'title')

prefixing with dot will also be a way to avoid conflicts with plugins

{{ super.title }} plugin
{{ .super.title }} data

{{ libraries as library }}
    {{ categories as category }}
        {{ posts as post }}
            {{ .library.title }}
            {{ .category.title }}
            {{ .post.title }}
        {{ /posts }}
    {{ categories }}
{{ /libraries }}