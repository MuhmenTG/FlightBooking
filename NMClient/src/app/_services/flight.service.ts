import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment.development';
import { SearchFlightsRequest } from '../_models/Flights/SearchFlightsRequest';
import { FlightResponse } from '../_models/Flights/FlightResponse';
import { FlightInfoResponse } from '../_models/Flights/FlightInfoResponse';
import { FlightBookingResponse } from '../_models/Flights/FlightBookingResponse';
import { PaymentInfo } from '../_models/PaymentInfo';
import { FlightOffer } from '../_models/Flights/FlightOffer';

@Injectable({
  providedIn: 'root'
})
export class FlightService {
  private apiUrl = environment.apiUrl + "/flight";

  constructor(private http: HttpClient) { }

  httpOptions = {};

  // getCarrier(carrierCode: string): Observable<[]> {
  //   return this.http.get<[]>(this.amadeusUrl + "reference-data/airlines?airlineCodes=" + carrierCode, this.httpOptions);
  // }

  getFlights(body: SearchFlightsRequest): Observable<FlightResponse[]> {
    return this.http.post<FlightResponse[]>(this.apiUrl + "/searchFlights", JSON.stringify(body), this.httpOptions);
  }

  getFlightInfo(flightOffer: FlightResponse): Observable<FlightInfoResponse> {
    return this.http.post<FlightInfoResponse>(this.apiUrl + "/chooseFlightOffer", JSON.stringify(flightOffer), this.httpOptions);
  }

  getFlightConfirmation(bookingInfo: FlightOffer): Observable<FlightBookingResponse> {
    return this.http.post<FlightBookingResponse>(this.apiUrl + "/confirmFlight", JSON.stringify(bookingInfo), this.httpOptions);
  }

  getPaymentConfirmation(paymentInfo: PaymentInfo): Observable<{}> {
    return this.http.post<{}>(this.apiUrl + "/payConfirmFlight", JSON.stringify(paymentInfo), this.httpOptions);
  }
}