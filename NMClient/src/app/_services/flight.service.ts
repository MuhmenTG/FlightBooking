import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment.development';
import { AccessTokenResponse } from '../_models/AccessTokenResponse';
import { SearchFlightsResponse } from '../_models/SearchFlightsResponse';
import { SearchFlightsRequest } from '../_models/SearchFlightsRequest';
import { CustomerInfo } from '../_models/CustomerInfo';

@Injectable({
  providedIn: 'root'
})
export class FlightService {

  private apiUrl = environment.apiUrl + "/flight";
  private amadeusUrl = environment.amadeusApiUrl;

  private accessTokenParameters = new HttpParams()
    .set('grant_type', 'client_credentials')
    .set('client_id', '73Vz7GstLSf9xaHWx0fPMH6PRg6wYqjT')
    .set('client_secret', 'lSJEDh6AwHwH5omy')

  private body: SearchFlightsRequest = { originLocationCode: "AXX", destinationLocationCode: "YYZ", departureDate: "2023-04-15", returnDate: "2023-04-21", adults: 1 };

  private httpOptions = {};

  constructor(private http: HttpClient) { }

  setHttpOptions(): void {
    this.httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/x-www-form-urlencoded',
        'Authorization': "Bearer " + environment.access_token
      })
    }
  }

  getAccessToken(): Observable<AccessTokenResponse> {
    return this.http.post<AccessTokenResponse>(this.amadeusUrl, this.accessTokenParameters);
  }
  // searchCriteria: SearchFlightsRequest
  getFlights(): Observable<SearchFlightsResponse[]> {
    console.log(this.httpOptions);
    return this.http.post<SearchFlightsResponse[]>(this.apiUrl + "/searchFlights", this.body, this.httpOptions);
  }

  getFlightInfo(flightOffer: SearchFlightsResponse): Observable<{}> {
    return this.http.post<{}>(this.apiUrl + "/selectFlight", flightOffer, this.httpOptions);
  }

  getFlightConfirmation(customerInfo: CustomerInfo): Observable<SearchFlightsResponse[]> {
    return this.http.post<SearchFlightsResponse[]>(this.apiUrl + "/confirmFlight", customerInfo, this.httpOptions);
  }
}