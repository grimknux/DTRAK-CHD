<html>
    <head>
        <title>{page_title|lower}</title>
    </head>
    <body>
        <h1>{page_heading|upper|limit_chars(5)}</h1>
        <p>DOB: {date|date(l dS F Y)}</p>
        <p>DOB: {date|date_modify(+5days)|date(l dS F Y)}</p>
        <p>Price: {price|local_currency(EUR)|highlight_code}</p>
        <p>Price: {price_one|round(ceil)}</p>
        <h1>Applying Custome Filters</h1>
        <p>Mobile: {mobile|hidenumbers()}</p>
        <h1>{page_heading} contains {page_heading|countvowels} vowels</h1>

        
    </body>

</html>
