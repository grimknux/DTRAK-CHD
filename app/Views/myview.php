<html>
    <head>
        <title>{page_title}</title>
    </head>
    <body>
        <h1>{page_heading}</h1>
        <h3>Subject List</h3>
            
            {subject_list}

            <h1>{subject}</h1>
            <p>{abbr}</p>

            {/subject_list}

            {if $status}
                <p>Welcome master</p>
            {endif}
            
    </body>

</html>
