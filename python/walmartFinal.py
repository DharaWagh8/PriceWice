from bs4 import BeautifulSoup
import json
import requests
import extruct
from w3lib.html import get_base_url
from typing import Optional, List
import logging

import http.client

"""http.client.HTTPConnection.debuglevel = 1

logging.basicConfig()
logging.getLogger().setLevel(logging.DEBUG)
requests_log = logging.getLogger("requests.packages.urllib3")
requests_log.setLevel(logging.DEBUG)
requests_log.propagate = True"""

# Function to extract Product Title
def get_title(soup):
	
	try:
		# Outer Tag Object
		title = soup.find("h1", attrs={"id":'main-title'})

		# Inner NavigatableString Object
		title_value = title.string

		# Title as a string value
		title_string = title_value.strip()

		# # Printing types of values for efficient understanding
		# print(type(title))
		# print(type(title_value))
		# print(type(title_string))
		# print()

	except AttributeError:
		title_string = ""	

	return title_string

# Function to extract Product Price
def get_priceNew(soup):

	try:
		price = soup.find("span",{"itemprop":"price"}).text

	except AttributeError:

		try:
			# If there is some deal price
			price="try"#price = soup.find("span", attrs={'id':'priceblock_dealprice'}).string.strip()
            

		except:		
			price = ""	

	return price

# Function to extract Product Rating
def get_rating(soup):

	try:
		rating = soup.find("i", attrs={'class':'a-icon a-icon-star a-star-4-5'}).string.strip()
		
	except AttributeError:
		
		try:
			rating = soup.find("span", attrs={'class':'a-icon-alt'}).string.strip()
		except:
			rating = ""	

	return rating

# Function to extract Number of User Reviews
def get_review_count(soup):
	try:
		review_count = soup.find("span", attrs={'id':'acrCustomerReviewText'}).string.strip()
		
	except AttributeError:
		review_count = ""	

	return review_count

# Function to extract Availability Status
def get_availability(soup):
	try:
		available = soup.find("div", attrs={'id':'availability'})
		available = available.find("span").string.strip()

	except AttributeError:
		available = "Not Available"	

	return available	

# Function to extract script Tags
def getPriceFromScript(soup):
	price = "None"
	try:
		all_data = soup.find_all("script", {"type": "application/ld+json"})
        
		for data in all_data:
			jsn = json.loads(data.string)
			price = jsn['offers']['price']
			break
			#print(jsn)
			#print(json.dumps(jsn, indent = 4))

	except AttributeError:
		price = "Not Available"	

	return price	

def get_price(json_ld: dict) -> Optional[str]:
    """
    Fetch title via extruct.

    :param dict json_ld: Parsed JSON-LD metadata from URL.

    :returns: Optional[str]
    """
    price =  json_ld['@graph'][2]['offers']['price'] + " " + json_ld['@graph'][2]['offers']['priceCurrency']
    return price

if __name__ == '__main__':

	# Headers for request
	HEADERS = ({'User-Agent':
	            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 Safari/537.36',
	            'Accept-Language': 'en-US'})

	# The webpage URL
	URL = "https://www.walmart.ca/en/browse/grocery/fruits-vegetables/10019_6000194327370"
	
	# HTTP Request
	webpage = requests.get(URL, headers=HEADERS)

	# Soup Object containing all data
	soup = BeautifulSoup(webpage.content, "lxml")

	pages = soup.select('section .mb0.ph1.pa0-xl.bb a')
	#pages =  soup.select("nav[aria-label='pagination'] li a")
	#print(pages);
	pageLinks = []
	for page in pages:
		pageLinks.append(page.get('href'))
		#print(pageLinks)


#exit()
"""		
for pageLink in pageLinks:
		print(pageLink)
		newPage = requests.get("https://www.walmart.ca" + pageLink, headers=HEADERS)
		newSoup = BeautifulSoup(newPage.content, "lxml")
		links = newSoup.select("div.h-100.pb1-xl.pr4-xl.pv1.ph1 a")
		print(len(links))
		# Fetch links as List of Tag Objects
        
        #links = soup.find_all("span", attrs={'data-automation-id':'product-title'})
        
        

	
	
        
	


		
	

    
#exit()
    
	# Store the links
links_list = [] 
    

	# Loop for extracting links from Tag Objects
for link in links:
		links_list.append(link.get('href'))
		
        
"""
	# Loop for extracting product details from each link 
for link in pageLinks:
		print(link)
		new_webpage = requests.get("https://www.walmart.ca" + link, headers=HEADERS)
		print(new_webpage)
        
        
		new_soup = BeautifulSoup(new_webpage.content, "lxml")
		#metadata = extruct.extract(new_webpage.content, base_url=get_base_url(new_webpage.content, "https://www.instacart.ca" + link), syntaxes=['json-ld'])['json-ld'][0]

		# Function calls to display all necessary product information
		print("Product Title =", get_title(new_soup))
		print("Product Price =", getPriceFromScript(new_soup))
		print("Product Rating =", get_rating(new_soup))
		print("Number of Product Reviews =", get_review_count(new_soup))
		print("Availability =", get_availability(new_soup))
		#print("meta =", get_price(metadata))
		
		#print("script =", scriptTags(new_soup))
		print()