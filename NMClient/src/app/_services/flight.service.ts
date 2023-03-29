import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment.development';
import { AccessTokenResponse } from '../_models/AccessTokenResponse';
import { SearchFlightsResponses } from '../_models/Flights/SearchFlightsResponses';
import { SearchFlightsRequest } from '../_models/Flights/SearchFlightsRequest';
import { FlightResponse } from '../_models/Flights/FlightResponse';
import { FlightInfoResponse, FlightOffer } from '../_models/Flights/FlightInfoResponse';

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

  private httpOptions = {};

  constructor(private http: HttpClient) { }

  setHttpOptions(): void {
    this.httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
        'Authorization': "Bearer " + environment.access_token
      })
    }
  }

  getAccessToken(): Observable<AccessTokenResponse> {
    return this.http.post<AccessTokenResponse>(this.amadeusUrl, this.accessTokenParameters);
  }

  getFlights(body: SearchFlightsRequest): Observable<SearchFlightsResponses> {
    return this.http.post<SearchFlightsResponses>(this.apiUrl + "/searchFlights", JSON.stringify(body), this.httpOptions);
  }

  getFlightInfo(flightOffer: FlightResponse): Observable<FlightInfoResponse> {
    return this.http.post<FlightInfoResponse>(this.apiUrl + "/chooseFlightOffer", JSON.stringify(flightOffer), this.httpOptions);
  }

  getFlightConfirmation(bookingInfo: FlightOffer): Observable<{}> {
    return this.http.post<{}>(this.apiUrl + "/confirmFlight", JSON.stringify(bookingInfo), this.httpOptions);
  }
}