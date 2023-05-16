import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment.development';
import { SearchFlightsRequest } from '../_models/Flights/SearchFlightsRequest';
import { FlightResponse, FlightResponses } from '../_models/Flights/FlightResponse';
import { FlightInfoResponse } from '../_models/Flights/FlightInfoResponse';
import { FlightBookingResponse } from '../_models/Flights/FlightBookingResponse';
import { PaymentInfo } from '../_models/PaymentInfo';
import { FlightOffer } from '../_models/Flights/FlightOffer';
import { HttpService } from './http.service';

@Injectable({
  providedIn: 'root'
})
export class FlightService extends HttpService{
  private apiUrl = environment.apiUrl + "/flight";

  // getCarrier(carrierCode: string): Observable<[]> {
  //   return this.http.get<[]>(this.amadeusUrl + "reference-data/airlines?airlineCodes=" + carrierCode, this.httpOptions);
  // }

  getFlights(body: SearchFlightsRequest): Observable<FlightResponses> {
    return this.http.post<FlightResponses>(this.apiUrl + "/searchFlights", JSON.stringify(body), HttpService.httpOptionsBearer);
  }

  getFlightInfo(flightOffer: FlightResponse): Observable<FlightInfoResponse> {
    return this.http.post<FlightInfoResponse>(this.apiUrl + "/chooseFlightOffer", JSON.stringify(flightOffer), HttpService.httpOptionsBearer);
  }

  getFlightConfirmation(bookingInfo: FlightOffer): Observable<FlightBookingResponse> {
    return this.http.post<FlightBookingResponse>(this.apiUrl + "/confirmFlight", JSON.stringify(bookingInfo), HttpService.httpOptionsBearer);
  }

  getPaymentConfirmation(paymentInfo: PaymentInfo): Observable<{}> {
    return this.http.post<{}>(this.apiUrl + "/payConfirmFlight", JSON.stringify(paymentInfo), this.httpOptions);
  }
}