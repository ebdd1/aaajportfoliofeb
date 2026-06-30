<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/">
    <channel>
        <title>{{ $siteName }}</title>
        <link>{{ $siteUrl }}</link>
        <description>{{ $siteDescription }}</description>
        <language>id-ID</language>
        <copyright>Copyright {{ date('Y') }} {{ $siteName }}</copyright>
        <managingEditor>hello@febryanus.my.id ({{ $siteName }})</managingEditor>
        <webMaster>hello@febryanus.my.id ({{ $siteName }})</webMaster>
        <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
        <sy:updatePeriod>daily</sy:updatePeriod>
        <sy:updateFrequency>1</sy:updateFrequency>
        <atom:link href="{{ $siteUrl }}/blog/feed" rel="self" type="application/rss+xml" />

        @foreach ($posts as $post)
        <item>
            <title><![CDATA[{{ $post->title }}]]></title>
            <link>{{ $siteUrl }}/blog/{{ $post->slug }}</link>
            <guid isPermaLink="true">{{ $siteUrl }}/blog/{{ $post->slug }}</guid>
            <description><![CDATA[{{ $post->excerpt ?? Str::limit(strip_tags($post->content), 160) }]]]></description>
            <content:encoded><![CDATA[{{ $post->content }]]]></content:encoded>
            <dc:creator><![CDATA[{{ $post->author?->name ?? $siteName }]]></dc:creator>
            <pubDate>{{ $post->published_at?->toRfc2822String() ?? now()->toRfc2822String() }}</pubDate>
            @if ($post->updated_at)
            <updated>{{ $post->updated_at->toRfc3339String() }}</updated>
            @endif
            @foreach ($post->categories as $category)
            <category><![CDATA[{{ $category->name }}]]></category>
            @endforeach
            @foreach ($post->tags as $tag)
            <category domain="tag"><![CDATA[{{ $tag->name }}]]></category>
            @endforeach
            @if ($post->featured_image_url)
            <enclosure url="{{ $post->featured_image_url }}" type="image/jpeg" length="0" />
            @endif
        </item>
        @endforeach
    </channel>
</rss>
